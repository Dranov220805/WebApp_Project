class Notes {
    constructor() {
        this.currentPage = 1;
        this.limit = 10;
        this.isLoading = false;

        this.loadNotes();

        document.addEventListener("DOMContentLoaded", () => {
            const noteContainer = document.querySelector(".other-note__load");
            if (noteContainer) {
                const notesInstance = new Notes();
                window.refreshNotes = () => notesInstance.loadNotes(); // optional manual refresh
            }
        });

        window.addEventListener("scroll", () => {
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 200) {
                this.loadNotes();
            }
        });
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
                    this.showToast('Notes loaded successfully!');
                } else {
                    this.showToast('No more notes to load.');
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
}

const notesInstance = new Notes();
window.refreshNotes = () => notesInstance.loadNotes(); // optional manual refresh

// export default notesInstance;
