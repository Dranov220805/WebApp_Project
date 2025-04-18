class Notes {
    constructor() {
        this.currentPage = 1;
        this.limit = 10;
        this.isLoading = false;

        this.loadNotes();
        this.inputNote();
        this.initNotePostTrigger();

        // document.addEventListener("DOMContentLoaded", () => {
        //     const pinnedNoteContainer = document.querySelector(".pinned-note__load");
        //     const noteContainer = document.querySelector(".other-note__load");
        //     if (noteContainer) {
        //         const notesInstance = new Notes();
        //         window.refreshNotes = () => notesInstance.loadNotes(); // optional manual refresh
        //     }
        // });
        //
        // window.addEventListener("scroll", () => {
        //     if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 200) {
        //         this.loadNotes();
        //     }
        // });

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

    // loadNotes() {
    //     if (this.isLoading) return;
    //     this.isLoading = true;
    //
    //     fetch(`/note/list?page=${this.currentPage}&limit=${this.limit}`, {
    //         headers: {
    //             'Authorization': 'Bearer ' + localStorage.getItem('accessToken')
    //         }
    //     })
    //         .then(res => res.json())
    //         .then(data => {
    //             if (data.data?.length > 0) {
    //                 this.appendNotesToDOM(data.data);
    //                 this.currentPage++;
    //             } else {
    //                 console.log("No more notes");
    //             }
    //         })
    //         .catch(err => console.error('Fetch failed:', err))
    //         .finally(() => this.isLoading = false);
    // }

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

        this.showToast('Loading notes...');

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
            div.className = "note-sheet";
            div.innerHTML = `
            <div style="padding: 16px;">
                <h3 style="color: #202124; font-size: 16px; font-weight: 500; margin: 0 0 12px 0;">
                    ${note.title}
                </h3>
                <div style="color: #5f6368; font-size: 14px; line-height: 1.5;">
                    ${note.content.split(',').map(item => `<div>- ${item.trim()}</div>`).join('')}
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px;">
                <div>
                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                            <path d="M13 9h-2v2H9v2h2v2h2v-2h2v-2h-2z"/>
                            <path d="M12 20c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-18C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                        </svg>
                    </button>
                    <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                            <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16zM16 17H5V7h11l3.55 5L16 17z"/>
                        </svg>
                    </button>
                </div>
                <button style="background: none; border: none; cursor: pointer; padding: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#5f6368">
                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
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

            // If user starts typing and contentTextarea doesn't exist
            if (trimmedValue !== "" && !contentTextarea) {
                contentTextarea = document.createElement("textarea");
                contentTextarea.className = "note-text__content";
                contentTextarea.placeholder = "Write your note...";
                contentTextarea.rows = 3;

                Object.assign(contentTextarea.style, {
                    border: "none",
                    outline: "none",
                    fontSize: "16px",
                    marginTop: "12px",
                    resize: "none",
                    width: "100%",
                    overflow: "hidden",
                    height: "0px",
                    opacity: "0",
                    transition: "height 0.3s ease, opacity 0.3s ease"
                });

                this.parentNode.insertBefore(contentTextarea, this.nextSibling);

                setTimeout(() => {
                    contentTextarea.style.height = "80px";
                    contentTextarea.style.opacity = "1";
                }, 1);

                contentTextarea.addEventListener("input", function () {
                    this.style.height = "auto";
                    this.style.height = this.scrollHeight + "px";
                });
            }

            // If input is cleared, remove contentTextarea
            if (trimmedValue === "" && contentTextarea) {
                contentTextarea.style.height = "0px";
                contentTextarea.style.opacity = "0";

                // Instead of setTimeout, listen for transition end
                contentTextarea.addEventListener("transitionend", function handleTransitionEnd() {
                    if (contentTextarea && contentTextarea.parentNode) {
                        contentTextarea.remove();
                        contentTextarea = null;
                    }

                    // Remove listener to avoid multiple triggers
                    this.removeEventListener("transitionend", handleTransitionEnd);
                });
            }
        });
    }

}

const notesInstance = new Notes();
window.refreshNotes = () => notesInstance.loadNotes(); // optional manual refresh

// export default notesInstance;
