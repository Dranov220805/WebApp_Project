class Notes {
    constructor() {
        this.currentPage = 1;
        this.limit = 10;
        this.isLoading = false;
        this.lastScrollTop = 0;

        this.setupEvents();
        this.loadNotes();
    }

    setupEvents() {
        const createNoteBtn = document.querySelector(".create-note-btn");
        const noteInput = document.querySelector(".note-post__input");

        if (createNoteBtn) createNoteBtn.addEventListener("click", () => this.createNote_POST());
        if (noteInput) noteInput.addEventListener("input", this.autoResizeInput);

        window.addEventListener("scroll", () => this.handleScroll());
        this.initNotePostTrigger();
    }

    handleScroll() {
        const currentScrollTop = window.scrollY;
        if (currentScrollTop > this.lastScrollTop &&
            (window.innerHeight + currentScrollTop >= document.body.offsetHeight - 200)) {
            this.loadNotes();
        }
        this.lastScrollTop = Math.max(currentScrollTop, 0);
    }

    showToast(message, type = 'danger', duration = 3000) {
        const toast = document.getElementById("toast");
        const messageElement = document.getElementById("toast-message");
        const closeBtn = document.getElementById("toast-close");

        if (!toast || !messageElement || !closeBtn) return;

        messageElement.innerText = message;
        toast.classList.remove("d-none", "bg-success", "bg-danger");
        toast.classList.add(`bg-${type}`);

        toast.classList.remove("d-none");

        const hideTimeout = setTimeout(() => toast.classList.add("d-none"), duration);
        closeBtn.onclick = () => {
            toast.classList.add("d-none");
            clearTimeout(hideTimeout);
        };
    }

    loadNotes() {
        if (this.isLoading) return;
        this.isLoading = true;

        fetch(`/note/list?page=${this.currentPage}&limit=${this.limit}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('accessToken')
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
            .finally(() => this.isLoading = false);
    }

    appendNotesToDOM(notes) {
        const container = document.querySelector(".other-note__load");
        if (!container) return;

        notes.forEach(note => {
            const div = document.createElement("div");
            div.className = "note-sheet d-flex flex-column";
            div.innerHTML = `
                <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
                    <h3 class="note-sheet__title">${note.title}</h3>
                    <div class="note-sheet__content">
                        ${note.content.split(',').map(item => `<div>- ${item.trim()}</div>`).join('')}
                    </div>
                </div>
                <div class="note-sheet__menu">
                    <div>
                        <button><i class="fa-regular fa-square-plus"></i></button>
                        <button><i class="fa-solid fa-tags"></i></button>
                    </div>
                    <button><i class="fa-solid fa-ellipsis-vertical"></i></button>
                </div>
            `;
            container.appendChild(div);
        });
    }

    appendNewNotesToDOM(note) {
        const container = document.querySelector(".other-note__load");
        if (!container) return;

        const div = document.createElement("div");
        div.className = "note-sheet d-flex flex-column";
        div.innerHTML = `
            <div class="note-sheet" onclick="notesInstance.openNoteModal('${note.title}', '${note.content}')">
                <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
                    <h3 class="note-sheet__title">${note.title}</h3>
                    <div class="note-sheet__content">
                        ${note.content.split(',').map(item => `<div>- ${item.trim()}</div>`).join('')}
                    </div>
                </div>
                <div class="note-sheet__menu" onclick="event.stopPropagation()">
                    <div>
                        <button><i class="fa-regular fa-square-plus"></i></button>
                        <button><i class="fa-solid fa-tags"></i></button>
                    </div>
                    <button><i class="fa-solid fa-ellipsis-vertical"></i></button>
                </div>
            </div>
        `;
        container.prepend(div);
    }

    autoResizeInput(event) {
        event.target.style.height = 'auto';
        event.target.style.height = `${event.target.scrollHeight}px`;
    }

    openNoteModal(title, content) {
        // Update the modal with the note title and content
        document.getElementById("noteModalLabel").textContent = title;
        document.getElementById("modalContent").innerHTML = content
            .split(',')
            .map(item => `<div>- ${item.trim()}</div>`)
            .join('');
    }

    initNotePostTrigger() {
        const inputArea = document.querySelector(".note-post__input");
        const titleField = document.querySelector(".note-text__content");

        if (!inputArea || !titleField || inputArea.dataset.listenerAttached === "true") return;
        inputArea.dataset.listenerAttached = "true";

        // Resize inputArea dynamically as user types
        inputArea.addEventListener("input", function () {
            const value = this.value.trim();

            if (value !== "") {
                // Show the title field if hidden
                if (titleField.classList.contains("d-none")) {
                    requestAnimationFrame(() => {
                        titleField.style.height = "23px";
                        titleField.style.opacity = "1";
                        titleField.classList.remove("d-none");
                    });
                }

                // Auto-resize the inputArea (optional)
                this.style.height = "auto";
                this.style.height = this.scrollHeight + "px";
            } else {
                // Hide the title field
                titleField.style.height = "0px";
                titleField.style.opacity = "0";
                titleField.classList.add("d-none");

                // Optional: clear title input when hidden
                titleField.addEventListener("transitionend", function handleEnd() {
                    titleField.value = "";
                    titleField.removeEventListener("transitionend", handleEnd);
                });
            }
        });

        // Auto-resize titleField as user types
        titleField.addEventListener("input", function () {
            this.style.height = "auto";
            this.style.height = this.scrollHeight + "px";
        });
    }





    createNote_POST() {
        const titleInput = document.querySelector(".note-text__content");
        const contentInput = document.querySelector(".note-post__input");

        const title = titleInput?.value.trim() || '';
        const content = contentInput?.value.trim() || '';

        console.log(titleInput);
        console.log(contentInput);
        console.log(title);
        console.log(content);

        if (!title || !content) {
            this.showToast('Please enter a title and content', 'danger');
            return;
        }

        fetch('note/create', {
            method: 'POST',
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                title,
                content
            })
        })
            .then(response => response.json())
            .then(data => {
                const { status, accountId, title, content, createdDate, message} = data;
                if (status === true) {
                    this.showToast('Note created successfully!', 'success');
                    titleInput.remove();
                    contentInput.value = '';
                    this.appendNewNotesToDOM(data);
                } else {
                    this.showToast(data.message || 'Failed to create note.', 'danger');
                }
            })
            .catch(err => {
                console.error('Create note error:', err);
                this.showToast('An error occurred while creating the note.', 'danger');
            });
    }


}

// Initialize
const notesInstance = new Notes();
window.refreshNotes = () => notesInstance.loadNotes();
