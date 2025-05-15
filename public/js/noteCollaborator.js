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

        // REMOVE THIS LINE:
        // if (message.editorEmail === this.userEmail) return;
        // The server already ensures that updates are not sent back to the originator.
        // Any 'update' message received here is from another editor.

        console.log('Received update from:', message.editorEmail, '(My email:', this.userEmail, ') for note:', message.noteId);

        // Save selection/cursor position
        const isTitleFocused = document.activeElement === this.titleInput;
        const isContentFocused = document.activeElement === this.contentInput;
        const titleSelectionStart = isTitleFocused && this.titleInput ? this.titleInput.selectionStart : null;
        const titleSelectionEnd = isTitleFocused && this.titleInput ? this.titleInput.selectionEnd : null;
        const contentSelectionStart = isContentFocused && this.contentInput ? this.contentInput.selectionStart : null;
        const contentSelectionEnd = isContentFocused && this.contentInput ? this.contentInput.selectionEnd : null;


        // Update content
        if (message.title !== undefined && this.titleInput) {
            this.titleInput.value = message.title;
        }

        if (message.content !== undefined && this.contentInput) {
            this.contentInput.value = message.content;
        }

        // Restore cursor positions
        if (isTitleFocused && titleSelectionStart !== null && this.titleInput) {
            this.titleInput.setSelectionRange(titleSelectionStart, titleSelectionEnd);
        }

        if (isContentFocused && contentSelectionStart !== null && this.contentInput) {
            this.contentInput.selectionStart = contentSelectionStart;
            this.contentInput.selectionEnd = contentSelectionEnd;
        }

        // Dynamically check and refresh note sections
        // const sections = [
        //     { className: 'other-note__load', loader: this.loadNewNotes.bind(this) },
        //     { className: 'pinned-note__load', loader: this.loadNewPinnedNotes.bind(this) },
        //     { className: 'label-note__load', loader: this.loadNewLabelNote.bind(this) },
        //     // { className: 'share-note__load', loader: this.loadSharedNotes.bind(this) }
        // ];
        //
        // sections.forEach(section => {
        //     const element = document.querySelector(`.${section.className}`);
        //     if (element) {
        //         element.innerHTML = '';
        //         section.loader();
        //     }
        // });

        const noteElement = document.querySelector(`.note-sheet[data-note-id="${message.noteId}"]`);
        if (noteElement) {
            noteElement.querySelector('.note-sheet__title').textContent = message.title;
            noteElement.querySelector('.note-sheet__content').innerHTML = message.content.replace(/\n/g, '<br>');
            noteElement.dataset.noteTitle = message.title;
            noteElement.dataset.noteContent = message.content;
        }

        const noteCard = document.querySelector(`[data-note-id="${message.noteId}"]`);
        if (noteCard) {
            noteCard.dataset.noteTitle = message.title;
            noteCard.dataset.noteContent = message.content;

            const titleEl = noteCard.querySelector('.note--share__title');
            const contentEl = noteCard.querySelector('.note--share__content');
            if (titleEl) titleEl.textContent = message.title;
            if (contentEl) contentEl.textContent = message.content;
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

    loadNewNotes() {
        if (this.isLoadingNotes) return;
        this.isLoadingNotes = true;
        this.currentPage = 1;
        this.limit = 10;

        fetch(`/note/list?page=${this.currentPage}&limit=${this.limit}`, {
            headers: {
                'Authorization': 'Bearer '
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.data?.length > 0) {
                    this.appendNotesToDOM(data.data);
                    this.currentPage++;
                }
            })
            .catch(err => {
                console.error('Fetch failed:', err);
                this.showToast('Failed to load notes. Please try again.');
            })
            .finally(() => this.isLoadingNotes = false);
    }

    appendNotesToDOM(notes) {
        const container = document.querySelector(".other-note__load");
        if (!container) return;

        notes.forEach(note => {
            if (document.querySelector(`.note-sheet[data-note-id="${note.noteId}"]`)) {
                console.log(`Skipping duplicate note: ${note.noteId}`);
                return;
            }

            const div = document.createElement("div");
            div.className = "note-sheet d-flex";
            div.dataset.noteId = note.noteId;
            div.dataset.noteTitle = note.title;
            div.dataset.noteContent = note.content;

            if (note.imageLink && note.imageLink.trim() !== '') {
                div.dataset.imageLink = note.imageLink;
            }

            if (note.labels && note.labels.length > 0) {
                div.dataset.labels = JSON.stringify(note.labels);
            }

            let labels = [];
            if (div.dataset.labels) {
                try {
                    labels = JSON.parse(div.dataset.labels);
                } catch (e) {
                    console.error("Failed to parse labels:", e);
                }
            }

            const imageHTML = note.imageLink && note.imageLink.trim() !== ''
                ? `<div class="note-sheet__image" style="overflow-y: visible">
                   <img src="${note.imageLink}" style="display: block">
               </div>`
                : '';

            div.innerHTML = `
            ${imageHTML}
            <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
                <h3 class="note-sheet__title">${note.title}</h3>
                <div class="note-sheet__content" style="overflow-x: hidden">
                    ${note.content.replace(/\n/g, '<br>')}
                </div>
            </div>
            <div class="note-sheet__menu">
                <div class="note-sheet__menu--item">
                    <button class="note-pin-btn" title="Pin Note"><i class="fa-solid fa-thumbtack-slash"></i></button>
                    <button title="Add Label" data-bs-target="listLabelNoteModal" id="note-label-list-btn" class="note-label-list-btn"><i class="fa-solid fa-tags"></i></button>
                    <button class="note-delete-btn" title="Delete This Note" data-bs-target="deleteNoteModal" data-note-id="${note.noteId}"><i class="fa-solid fa-trash"></i></button>
                    <button class="note-share-btn" title="Share this Note"><i class="fa-solid fa-users"></i></button>
                    <button class="note-lock-btn" title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
                </div>
            </div>
        `;

            container.appendChild(div);
        });
    }

    loadNewPinnedNotes() {
        if (this.isLoadingPinnedNotes) return;
        this.isLoadingPinnedNotes = true;
        this.currentPage = 1;
        this.limit = 100;

        fetch(`/note/pinned-list?page=${this.currentPage}&limit=${this.limit}`, {
            headers: {
                'Authorization': 'Bearer '
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.data?.length > 0) {
                    this.appendPinnedNotesToDOM(data.data);
                    this.currentPage++;
                }
            })
            .catch(err => {
                console.error('Fetch failed:', err);
                this.showToast('Failed to load notes. Please try again.');
            })
            .finally(() => this.isLoadingPinnedNotes = false);
    }

    appendPinnedNotesToDOM(notes) {
        const container = document.querySelector(".pinned-note__load");
        if (!container) return;

        notes.forEach(note => {
            if (document.querySelector(`.note-sheet[data-note-id="${note.noteId}"]`)) {
                console.log(`Skipping duplicate note: ${note.noteId}`);
                return;
            }

            const div = document.createElement("div");
            div.className = "note-sheet d-flex";
            div.dataset.noteId = note.noteId;
            div.dataset.noteTitle = note.title;
            div.dataset.noteContent = note.content;

            if (note.imageLink && note.imageLink.trim() !== '') {
                div.dataset.imageLink = note.imageLink;
            }

            if (note.labels && note.labels.length > 0) {
                div.dataset.labels = JSON.stringify(note.labels);
            }

            let labels = [];
            if (div.dataset.labels) {
                try {
                    labels = JSON.parse(div.dataset.labels);
                } catch (e) {
                    console.error("Failed to parse labels:", e);
                }
            }

            const imageHTML = note.imageLink && note.imageLink.trim() !== ''
                ? `<div class="note-sheet__image" style="overflow-y: visible">
                   <img src="${note.imageLink}" style="display: block">
               </div>`
                : '';

            div.innerHTML = `
                ${imageHTML}
                <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
                    <h3 class="note-sheet__title">${note.title}</h3>
                    <div class="note-sheet__content" style="overflow-x: hidden">
                        ${note.content.replace(/\n/g, '<br>')}
                    </div>
                </div>
                <div class="note-sheet__menu">
                    <div class="note-sheet__menu--item">
                        <button class="pinned-note-pin-btn" title="Unpin Note"><i class="fa-solid fa-thumbtack"></i></button>
                        <button title="Add Label" data-bs-target="listLabelNoteModal" id="note-label-list-btn" class="note-label-list-btn"><i class="fa-solid fa-tags"></i></button>
                        <button class="pinned-note-delete-btn" title="Delete This Note" data-bs-target="deleteNoteModal" data-note-id="${note.noteId}"><i class="fa-solid fa-trash"></i></button>
                        <button class="note-share-btn" title="Share this Note"><i class="fa-solid fa-users"></i></button>
                        <button class="note-lock-btn" title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
                    </div>
                </div>
            `;
            container.appendChild(div);
        });
    }

    loadNewLabelNote() {
        if (this.isLoadingNotes) return;
        this.isLoadingNotes = true;

        this.labelName = document.getElementById("note-layout__title")?.textContent?.trim() || '';

        const encodedLabel = encodeURIComponent(this.labelName).replace(/%20/g, '+');

        fetch(`/note/label/${encodedLabel}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('accessToken')
            }
        })
            .then(res => res.json())
            .then(data => {
                console.log(data);
                if (data.data?.length > 0) {
                    this.appendLabelNotesToDOM(data.data);
                    this.currentPage++;
                }
            })
            .catch(err => {
                console.error('Fetch failed:', err);
                this.showToast('Failed to load notes. Please try again.');
            })
            .finally(() => this.isLoadingNotes = false);
    }

    appendLabelNotesToDOM(notes) {
        console.log(notes);
        const container = document.querySelector(".label-note__load");
        if (!container) return;

        notes.forEach(note => {
            if (document.querySelector(`.note-sheet[data-note-id="${note.noteId}"]`)) {
                console.log(`Skipping duplicate note: ${note.noteId}`);
                return;
            }

            const div = document.createElement("div");
            div.className = "note-sheet note-sheet-label d-flex";
            div.dataset.noteId = note.noteId;
            div.dataset.noteTitle = note.title;
            div.dataset.noteContent = note.content;

            if (note.imageLink && note.imageLink.trim() !== '') {
                div.dataset.imageLink = note.imageLink;
            }

            if (note.labels && note.labels.length > 0) {
                div.dataset.labels = JSON.stringify(note.labels);
            }

            let labels = [];
            if (div.dataset.labels) {
                try {
                    labels = JSON.parse(div.dataset.labels);
                } catch (e) {
                    console.error("Failed to parse labels:", e);
                }
            }

            console.log('All labels: ', labels);

            const imageHTML = note.imageLink && note.imageLink.trim() !== ''
                ? `<div class="note-sheet__image" style="overflow: visible">
                   <img src="${note.imageLink}" style="display: block">
               </div>`
                : '';

            div.innerHTML = `
            ${imageHTML}
            <div class="note-sheet__title-content flex-grow-1" style="padding: 16px;">
                <h3 class="note-sheet__title">${note.title}</h3>
                <div class="note-sheet__content" style="overflow-x: hidden">
                    ${note.content.replace(/\n/g, '<br>')}
                </div>
            </div>
            <div class="note-sheet__menu">
                <div class="note-sheet__menu--item">
                    <button title="Add Label" data-bs-target="listLabelNoteModal" id="note-label-list-btn" class="note-label-list-btn"><i class="fa-solid fa-tags"></i></button>
                    <button class="note-label-delete-btn" title="Delete This Note" data-bs-target="deleteNoteModal" data-note-id="${note.noteId}"><i class="fa-solid fa-trash"></i></button>
                    <button class="note-share-btn" title="Share this Note"><i class="fa-solid fa-users"></i></button>
                    <button class="note-lock-btn" title="This note is unlocked"><i class="fa-solid fa-unlock"></i></button>
                </div>
            </div>
        `;

            container.appendChild(div);
        });
    }

    loadSharedNotes() {

        fetch(`/note/share-list`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(res => res.json())
            .then(data => {
                console.log(data);
                if (data.data?.length > 0) {
                    this.appendTrashedNotesToDOM(data.data);
                    this.currentPage++;
                }
            })
            .catch(err => {
                console.error('Fetch failed:', err);
                this.showToast('Failed to load notes. Please try again.');
            })
            .finally(() => this.isLoadingTrash = false);
    }

    appendSharedNotesToDOM(notes) {
        const container = document.querySelector(".share-note__load");
        if (!container) return;

        // Group notes by noteId and aggregate labels (just like in PHP)
        const groupedNotes = {};
        notes.forEach(note => {
            const noteId = note.noteId;
            if (!groupedNotes[noteId]) {
                groupedNotes[noteId] = { ...note, labels: [] };
            }
            if (note.labelName && !groupedNotes[noteId].labels.includes(note.labelName)) {
                groupedNotes[noteId].labels.push(note.labelName);
            }
        });

        Object.values(groupedNotes).forEach(note => {
            if (document.querySelector(`.shared-note-card[data-note-id="${note.noteId}"]`)) {
                console.log(`Skipping duplicate note: ${note.noteId}`);
                return;
            }

            const noteDiv = document.createElement("div");
            noteDiv.className = "col-12";

            const canEditText = note.canEdit ? "Can edit" : "Read-only";
            const accessClass = note.canEdit ? "access-edit" : "access-readonly";

            const labelsHTML = (note.labels || []).map(label =>
                label ? `<span class="badge bg-secondary me-1">${label}</span>` : ''
            ).join("");

            const imageHTML = note.imageLink && note.imageLink.trim() !== ''
                ? `<div class="note--share__image">
                    <img src="${note.imageLink}" class="rounded" alt="User Avatar" style="margin-top: 0px; height: 100%; width: auto">
               </div>`
                : '';

            const labelAttr = JSON.stringify(note.labels || []);

            noteDiv.innerHTML = `
            <div class="card shared-note-card" style="max-height: 230px"
                data-note-id="${note.noteId}"
                data-note-title="${note.title || ''}"
                data-note-content="${note.content || ''}"
                ${note.imageLink ? `data-note-image="${note.imageLink}"` : ''}
                ${note.labels.length > 0 ? `data-note-labels='${labelAttr}'` : ''}
                data-note-edit="${note.canEdit ? 'true' : 'false'}">
                <div class="card-body">
                    <div class="d-flex justify-content-start" style="width: 100%; max-width: 100%;">
                        <div style="width: 100%">
                            <div class="small mb-1 note--share__by">
                                Shared by <strong>${note.sharedEmail}</strong>
                            </div>
                            <div class="small mb-2 note--share__time">
                                Shared on ${new Date(note.timeShared).toLocaleDateString('en-US', {
                month: 'short', day: '2-digit', year: 'numeric'
            })}
                            </div>
                            ${note.title ? `<h6 class="fw-bold note--share__title">${note.title}</h6>` : ''}
                            ${note.content ? `<p class="mb-1 note--share__content" style="overflow-y: hidden; max-height: 48px; padding-right: 20%">${note.content}</p>` : ''}
                            ${labelsHTML ? `<div class="mt-2">${labelsHTML}</div>` : ''}
                        </div>
                        <div class="text-end" style="display: flex; flex-direction: column; width: 120px; justify-content: space-between; align-items: end">
                            <span class="access-label ${accessClass}" style="width: fit-content">${canEditText}</span>
                            ${imageHTML}
                        </div>
                    </div>
                </div>
            </div>
        `;

            container.appendChild(noteDiv);
        });
    }
}