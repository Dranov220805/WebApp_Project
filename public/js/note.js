class Notes {
    constructor() {
        this.currentPage = 1;
        this.limit = 10;
        this.isLoading = false;

        this.loadNotes();
        this.inputNote();
        this.initNotePostTrigger();

        document.addEventListener("DOMContentLoaded", () => {
            const pinnedNoteContainer = document.querySelector(".pinned-note__load");
            const noteContainer = document.querySelector(".other-note__load");
            if (noteContainer) {
                const notesInstance = new Notes();
                window.refreshNotes = () => notesInstance.loadNotes(); // optional manual refresh
            }
        });

        window.addEventListener("scroll", () => this.handleScroll());
    }

    handleScroll() {
        const currentScrollTop = window.scrollY; // Get current scroll position

        // Check if scrolling down (current position > previous position)
        if (currentScrollTop > this.lastScrollTop) {
            // Only load more notes if we are near the bottom of the page
            if ((window.innerHeight + currentScrollTop) >= document.body.offsetHeight - 200) {
                this.loadNotes();
            }
        }

        // Update the last scroll position
        this.lastScrollTop = currentScrollTop <= 0 ? 0 : currentScrollTop; // Prevent negative scroll value
    }

    showToast(message, duration = 3000) {
        const toast = document.getElementById("toast");
        const messageElement = document.getElementById("toast-message");
        const closeBtn = document.getElementById("toast-close");

        // Set message
        messageElement.innerText = message;

        // Show toast
        toast.classList.remove("d-none");

        // Auto-hide after duration
        const hideTimeout = setTimeout(() => {
            toast.classList.add("d-none");
        }, duration);

        // Allow manual close
        closeBtn.onclick = () => {
            toast.classList.add("d-none");
            clearTimeout(hideTimeout);
        };
    };

    loadNotes() {
        if (this.isLoading) return;
        this.isLoading = true;

        // this.showToast('Loading notes...');

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
                    // this.showToast('Notes loaded successfully!');
                } else {
                    // this.showToast('No more notes to load.');
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

        notes.forEach(note => {
            const div = document.createElement("div");
            div.className = "note-sheet d-flex flex-column";
            div.innerHTML = `
            <div class="note-sheet__title-content flex-column flex-grow-1" style="padding: 16px;">
                <h3 class="note-sheet__title" style="color: #202124; font-size: 16px; font-weight: 500; margin: 0 0 12px 0;">
                    ${note.title}
                </h3>
                <div class="note-sheet__content" style="color: #5f6368; font-size: 14px; line-height: 1.5;">
                    ${note.content.split(',').map(item => `<div>- ${item.trim()}</div>`).join('')}
                </div>
            </div>
            <div class="note-sheet__menu" style="display: flex; justify-content: space-between; padding: 8px;">
                <div>
                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                            <i class="fa-regular fa-square-plus"></i>
                        </svg>
                    </button>
                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                            <i class="fa-solid fa-tags"></i>
                        </svg>
                    </button>
                </div>
                <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </svg>
                </button>
            </div>
        `;
            container.appendChild(div);
        });
    }

    inputNote() {
        document.querySelector('.note-post__input').addEventListener('input', function () {
            this.style.height = 'auto'; // Reset the height to auto to calculate new content height
            this.style.height = (this.scrollHeight) + 'px'; // Set the height to the scroll height of the content
        });
    }

    initNotePostTrigger() {
        const inputArea = document.querySelector(".note-post__input");

        // Prevent attaching the event listener multiple times
        if (inputArea.dataset.listenerAttached === "true") return;
        inputArea.dataset.listenerAttached = "true";

        let contentTextarea = null;

        inputArea.addEventListener("input", function () {
            const trimmedValue = this.value.trim();

            // Create the new textarea above the main input
            if (trimmedValue !== "" && !contentTextarea) {
                contentTextarea = document.createElement("textarea");
                contentTextarea.className = "note-text__content";
                contentTextarea.placeholder = "Title";
                contentTextarea.rows = 3;

                Object.assign(contentTextarea.style, {
                    border: "none",
                    outline: "none",
                    fontSize: "16px",
                    marginBottom: "12px",
                    resize: "none",
                    width: "100%",
                    overflow: "hidden",
                    height: "0px",
                    opacity: "0",
                    transition: "height 0.3s ease, opacity 0.3s ease"
                });

                // Insert ABOVE the main input
                this.parentNode.insertBefore(contentTextarea, this);

                requestAnimationFrame(() => {
                    contentTextarea.style.height = "23px";
                    contentTextarea.style.opacity = "1";
                });

                inputArea.addEventListener("input", function () {
                    this.style.height = "auto";
                    this.style.height = this.scrollHeight + "px";
                });
            }

            // If the main input is cleared, remove the above field
            if (trimmedValue === "" && contentTextarea) {
                contentTextarea.style.height = "0px";
                contentTextarea.style.opacity = "0";

                contentTextarea.addEventListener("transitionend", function handleTransitionEnd() {
                    if (contentTextarea && contentTextarea.parentNode) {
                        contentTextarea.remove();
                        contentTextarea = null;
                    }
                    this.removeEventListener("transitionend", handleTransitionEnd);
                });
            }
        });
    }

}

const notesInstance = new Notes();
window.refreshNotes = () => notesInstance.loadNotes(); // optional manual refresh

// export default notesInstance;
