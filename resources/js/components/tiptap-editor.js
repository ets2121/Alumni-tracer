import { Editor } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'
import Link from '@tiptap/extension-link'
import Placeholder from '@tiptap/extension-placeholder'

/**
 * Tiptap Editor Alpine Component
 * Use closure-based state for the editor instance to avoid Alpine Proxy conflicts
 * which cause "Mismatched transaction" errors.
 */
export default function tiptapEditor(config = {}) {
    let editorInstance = null;

    return {
        content: config.content || '',

        init() {
            // Already initialized?
            if (editorInstance) return;

            // Wait for next tick so $refs are available
            this.$nextTick(() => {
                try {
                    editorInstance = new Editor({
                        element: this.$refs.editor,
                        extensions: [
                            StarterKit.configure({
                                heading: {
                                    levels: [1, 2, 3],
                                },
                            }),
                            Link.configure({
                                openOnClick: false,
                                HTMLAttributes: {
                                    class: 'text-brand-600 underline',
                                },
                            }),
                            Placeholder.configure({
                                placeholder: config.placeholder || 'Write something...',
                            }),
                        ],
                        content: this.content,
                        onUpdate: ({ editor }) => {
                            // Update content property
                            this.content = editor.getHTML();
                        },
                        onTransaction: () => {
                            // Force re-renders for isActive checks if needed
                            // (Alpine will re-evaluate isActive because it's used in templates)
                        }
                    });
                    console.log('Tiptap editor initialized successfully (Closure Mode)');
                } catch (e) {
                    console.error('Failed to initialize Tiptap:', e);
                }
            });
        },

        isActive(type, opts = {}) {
            if (!editorInstance) return false;
            try {
                return editorInstance.isActive(type, opts);
            } catch (e) {
                return false;
            }
        },

        toggleHeading(level) {
            if (editorInstance) {
                editorInstance.chain().focus().toggleHeading({ level }).run();
            }
        },

        toggleBold() {
            if (editorInstance) {
                editorInstance.chain().focus().toggleBold().run();
            }
        },

        toggleBulletList() {
            if (editorInstance) {
                editorInstance.chain().focus().toggleBulletList().run();
            }
        },

        setLink() {
            if (!editorInstance) return;
            const url = window.prompt('URL', editorInstance.getAttributes('link').href);
            if (url === '') {
                editorInstance.chain().focus().extendMarkRange('link').unsetLink().run();
                return;
            }
            if (url) {
                editorInstance.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
            }
        },

        clearFormatting() {
            if (editorInstance) {
                editorInstance.chain().focus().unsetAllMarks().clearNodes().run();
            }
        },

        destroy() {
            if (editorInstance) {
                editorInstance.destroy();
                editorInstance = null;
            }
        }
    }
}
