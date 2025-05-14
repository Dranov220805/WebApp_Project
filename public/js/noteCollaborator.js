export class NoteCollaborator {
    constructor(noteId, titleInput, contentInput) {
        this.noteId = noteId;
        this.titleInput = titleInput;
        this.contentInput = contentInput;
        this.socket = null;
        this.typingTimeout = null;
        this.editorId = Math.random().toString(36).substring(2); // Generate unique editor ID
        this.isConnected = false;

        // Get user email for identification
        this.userEmail = localStorage.getItem('userEmail') || 'unknown@user.com';

        // Bind methods to preserve this context
        this.handleTitleInput = this.handleTitleInput.bind(this);
        this.handleContentInput = this.handleContentInput.bind(this);
    }

    connect() {
        // Close any existing connection
        if (this.socket) {
            this.disconnect();
        }

        try {
            // Use consistent port 8082 for WebSocket server
            this.socket = new WebSocket('ws://127.0.0.1:8082');

            this.socket.onopen = () => {
                console.log(`WebSocket connected for note: ${this.noteId}`);
                this.isConnected = true;

                // Subscribe to note updates
                this.socket.send(JSON.stringify({
                    type: 'subscribe',
                    noteId: this.noteId,
                    userEmail: this.userEmail
                }));

                // Add input listeners to the text fields
                this.addInputListeners();

                // Show connected status
                this.updateConnectionStatus(true);
            };

            this.socket.onmessage = (event) => {
                try {
                    const message = JSON.parse(event.data);
                    this.handleMessage(message);
                } catch (error) {
                    console.error('Error handling WebSocket message:', error);
                }
            };

            this.socket.onclose = () => {
                console.log('WebSocket connection closed');
                this.isConnected = false;
                this.updateConnectionStatus(false);

                // Remove input listeners when disconnected
                this.removeInputListeners();

                // Attempt to reconnect after 5 seconds
                setTimeout(() => {
                    if (!this.isConnected) {
                        console.log('Attempting to reconnect...');
                        this.connect();
                    }
                }, 5000);
            };

            this.socket.onerror = (error) => {
                console.error('WebSocket error:', error);
                this.updateConnectionStatus(false);
            };
        } catch (error) {
            console.error('Failed to create WebSocket connection:', error);
        }
    }

    disconnect() {
        this.removeInputListeners();

        if (this.socket) {
            // Send unsubscribe message
            if (this.socket.readyState === WebSocket.OPEN) {
                this.socket.send(JSON.stringify({
                    type: 'unsubscribe',
                    noteId: this.noteId,
                    userEmail: this.userEmail
                }));
            }

            this.socket.close();
            this.socket = null;
            this.isConnected = false;
            this.updateConnectionStatus(false);
        }
    }

    addInputListeners() {
        this.titleInput?.addEventListener('input', this.handleTitleInput);
        this.contentInput?.addEventListener('input', this.handleContentInput);
    }

    removeInputListeners() {
        this.titleInput?.removeEventListener('input', this.handleTitleInput);
        this.contentInput?.removeEventListener('input', this.handleContentInput);
    }

    handleTitleInput() {
        this.debounceSendUpdate();
    }

    handleContentInput() {
        this.debounceSendUpdate();
    }

    debounceSendUpdate() {
        clearTimeout(this.typingTimeout);

        // Show typing indicator immediately
        this.sendTypingIndicator(true);

        this.typingTimeout = setTimeout(() => {
            this.sendUpdate();

            // Stop typing indicator after a delay
            setTimeout(() => {
                this.sendTypingIndicator(false);
            }, 1000);
        }, 300); // Debounce delay
    }

    sendUpdate() {
        if (!this.socket || this.socket.readyState !== WebSocket.OPEN) return;

        this.socket.send(JSON.stringify({
            type: 'edit',
            noteId: this.noteId,
            title: this.titleInput?.value || '',
            content: this.contentInput?.value || '',
            editorEmail: this.userEmail,
            timestamp: Date.now()
        }));
    }

    sendTypingIndicator(isTyping) {
        if (!this.socket || this.socket.readyState !== WebSocket.OPEN) return;

        this.socket.send(JSON.stringify({
            type: 'typing_indicator',
            noteId: this.noteId,
            userEmail: this.userEmail,
            isTyping: isTyping
        }));
    }

    handleMessage(message) {
        switch (message.type) {
            case 'update':
                this.handleUpdateMessage(message);
                break;

            case 'user_joined':
                this.showCollaboratorActivity(message.userEmail, 'joined');
                break;

            case 'user_left':
                this.showCollaboratorActivity(message.userEmail, 'left');
                break;

            case 'typing_indicator':
                this.handleTypingIndicator(message);
                break;

            default:
                console.log('Unknown message type:', message.type);
        }
    }

    handleUpdateMessage(message) {
        // Only handle updates for the current note
        if (message.noteId !== this.noteId) return;

        // Ignore updates from the same user
        if (message.editorEmail === this.userEmail) return;

        console.log('Received update from:', message.editorEmail);

        // Save selection/cursor position
        const isTitleFocused = document.activeElement === this.titleInput;
        const isContentFocused = document.activeElement === this.contentInput;
        const titleSelectionStart = isTitleFocused ? this.titleInput.selectionStart : null;
        const contentSelectionStart = isContentFocused ? this.contentInput.selectionStart : null;

        // Update content
        if (message.title !== undefined && this.titleInput) {
            this.titleInput.value = message.title;
        }

        if (message.content !== undefined && this.contentInput) {
            this.contentInput.value = message.content;
        }

        // Restore cursor positions
        if (isTitleFocused && titleSelectionStart !== null) {
            this.titleInput.setSelectionRange(titleSelectionStart, titleSelectionStart);
        }

        if (isContentFocused && contentSelectionStart !== null) {
            this.contentInput.selectionStart = contentSelectionStart;
            this.contentInput.selectionEnd = contentSelectionStart;
        }

        // Show who made the changes
        this.showEditorActivity(message.editorEmail);
    }

    handleTypingIndicator(message) {
        // Ignore own typing indicators
        if (message.userEmail === this.userEmail) return;

        const typingIndicator = document.querySelector('.collaboration-typing-indicator');
        if (typingIndicator) {
            if (message.isTyping) {
                typingIndicator.textContent = `${message.userEmail} is typing...`;
                typingIndicator.classList.add('active');
            } else {
                typingIndicator.classList.remove('active');
            }
        }
    }

    showEditorActivity(email) {
        const activityElement = document.querySelector('.collaboration-activity');
        if (activityElement) {
            activityElement.textContent = `${email} edited this note`;
            activityElement.classList.add('active');

            setTimeout(() => {
                activityElement.classList.remove('active');
            }, 3000);
        }
    }

    showCollaboratorActivity(email, action) {
        const activityElement = document.querySelector('.collaboration-join-leave');
        if (activityElement) {
            activityElement.textContent = `${email} has ${action} the document`;
            activityElement.classList.add('active');

            setTimeout(() => {
                activityElement.classList.remove('active');
            }, 3000);
        }
    }

    updateConnectionStatus(connected) {
        const statusElement = document.querySelector('.collaboration-status');
        if (statusElement) {
            if (connected) {
                statusElement.classList.add('connected');
                statusElement.querySelector('span').textContent = 'Connected - Collaborative editing active';
            } else {
                statusElement.classList.remove('connected');
                statusElement.querySelector('span').textContent = 'Disconnected - Changes not synced';
            }
        }
    }
}