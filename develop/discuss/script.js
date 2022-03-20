$(() => {
    let userIcon = $(".user-icon")
    let userBar = $(".user-bar")
    let popOptions = {
        trigger: "focus",
        placement: "bottom",
        container: "body",
        content: () => userBar.html(),
        html: true
    }
    userIcon.popover(popOptions)
    let easymde = new EasyMDE({
        autoDownloadFontAwesome: false,
        element: $("#editor-area")[0],
        spellChecker: false,
        toolbar: [
            {
                name: "bold",
                action: EasyMDE.toggleBold,
                className: "bi-type-bold",
                title: "Bold"
            },
            {
                name: "italic",
                action: EasyMDE.toggleItalic,
                className: "bi-type-italic",
                title: "Italic"
            },
            {
                name: "strikethrough",
                action: EasyMDE.toggleStrikethrough,
                className: "bi-type-strikethrough",
                title: "Strikethrough"
            },
            {
                name: "heading",
                action: EasyMDE.toggleHeadingSmaller,
                className: "bi-type-h1",
                title: "Heading"
            },
            {
                name: "horizontal-rule",
                action: EasyMDE.drawHorizontalRule,
                className: "bi-dash-lg",
                title: "Insert Horizontal Line"
            },
            // {
            //     name: "clean-block",
            //     action: EasyMDE.cleanBlock,
            //     className: "bi-eraser",
            //     title: "Clean block"
            // },
            "|",
            {
                name: "quote",
                action: EasyMDE.toggleBlockquote,
                className: "bi-quote",
                title: "Quote"
            },
            {
                name: "unordered-list",
                action: EasyMDE.toggleUnorderedList,
                className: "bi-list-ul",
                title: "Generic List"
            },
            {
                name: "ordered-list",
                action: EasyMDE.toggleOrderedList,
                className: "bi-list-ol",
                title: "Numbered List"
            },
            {
                name: "table",
                action: EasyMDE.drawTable,
                className: "bi-table",
                title: "Insert Table"
            },
            {
                name: "image",
                action: EasyMDE.drawImage,
                className: "bi-image",
                title: "Insert Image"
            },
            {
                name: "link",
                action: EasyMDE.drawLink,
                className: "bi-link-45deg",
                title: "Create Link"
            },
            {
                name: "code",
                action: EasyMDE.toggleCodeBlock,
                className: "bi-code",
                title: "Code"
            },
            {
                name: "preview",
                action: EasyMDE.togglePreview,
                className: "bi-eye no-disable",
                title: "Toggle Preview"
            },
            "|",
            {
                name: "guide",
                action: "https://www.markdownguide.org/basic-syntax/",
                className: "bi-question-circle no-disable",
                title: "Markdown Guide"
            }
        ],
        maxHeight: "300px",
        autosave: {
            unique_id: "markdown",
            enabled: true
        }
    });
})