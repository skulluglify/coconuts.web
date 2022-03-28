$(() => {
    let userIcon = $(".user-icon")
    let userBar = $(".user-bar")
    const mediaQuery = window.matchMedia("(max-width: 500px)")
    let popOptions = {
        // trigger: "focus",
        strategy: "fixed",
        modifiers: [
          {
            name: "preventOverflow",
            options: {
              padding: 0
            }
          },
          {
            name: "offset",
            options: {
              offset: () => [0, mediaQuery.matches ? 0 : 8],
            },
          }
        ],
        // placement: "bottom-start",
        placement: "bottom",
        // container: "body",
        // content: () => userBar.html(),
        // html: true
    }
    // userIcon.popover(popOptions)
    let toggle = true;
    const popperInstance = Popper.createPopper(userIcon[0], userBar[0], popOptions)
    userIcon.on("click", function (e) {

    	if (!!toggle) {

			userBar.removeClass("d-none")
			userBar.show()
    		popperInstance.setOptions((options) => ({
	          ...popOptions,
	          modifiers: [
	            ...popOptions.modifiers,
	            {
	              name: "eventListeners",
	              enabled: true
	            }
	          ]
	        }))
    	} else {

			userBar.addClass("d-none")
			userBar.hide()
    		popperInstance.setOptions((options) => ({
	          ...popOptions,
	          modifiers: [
	            ...popOptions.modifiers,
	            {
	              name: "eventListeners",
	              enabled: false
	            }
	          ]
	        }))
    	}

    	toggle = !toggle
    })
    userIcon.on("focusout", function (e) {

        userBar.hide()
        popperInstance.setOptions((options) => ({
          ...popOptions,
          modifiers: [
            ...popOptions.modifiers,
            {
              name: "eventListeners",
              enabled: false
            }
          ]
        }))
        toggle = true;
    })
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

    // tricky
    $(".EasyMDEContainer .CodeMirror").bind("keydown", (e) => {

        if (e.ctrlKey || e.metaKey) {

            switch ((e.key || String.fromCodePoint(e.which)).toLowerCase()) {

                case "s":

                    e.preventDefault();
                    

                    Swal.fire({
                    
                        title: 'Do you want to save the changes?',
                        icon: "question",
                    
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: 'Save',
                        denyButtonText: `Don't save`,
                    
                    }).then((result) => {
                    
                        if (result.isConfirmed) {
                    
                            Swal.fire('Saved!', '', 'success')
                    
                        } else if (result.isDenied) {
                    
                            Swal.fire('Changes are not saved', '', 'info')
                            localStorage.setItem("smde_markdown", "")
                            
                        } else if (result.isDismissed) {
                            
                            
                            Swal.fire('Cencel!', '', 'error')
                        }
                    })
                break;
            }
        }
    })

    // categories
    // `<button class="wrap-categories-item btn btn-primary">
    // <i class="bi bi-x"></i>
    // <span>cplusplus</span>
    // </button>`

    !0 && (function main() {

        let categories = document.querySelector(".ds-editor-post .editor .categories .categories-preview")
        let textarea = document.querySelector(".ds-editor-post .editor .categories textarea")

        function createCategoryButton(context) {

            if (typeof context == "string") {

                let el = document.createElement("button")
                let spanel = document.createElement("span")
                let iel = document.createElement("i")
                el.classList.add("btn", "btn-primary", "wrap-categories-item", "animate__animated", "animate__bounceInLeft")
                iel.classList.add("bi", "bi-x")
                spanel.textContent = context
                el.append(iel, spanel)
        
                el.addEventListener("click", function click(e) {

                    // setTimeout(() => {
                    //     el.remove()
                    // }, 2000)

                    el.remove()
                })
                return el;
            }
            return null
        }

        textarea.addEventListener("keydown", (e) => {

            let c = (e.key || String.fromCodePoint(e.which)).toLowerCase()

            if (e.keyCode === 13) {

                let value = e.target.value
                
                if (!!value) {

                    value.split(",").forEach(text => {

                        text = text.trim()

                        if (!!text) {

                            let el = createCategoryButton(text)
                            if (!!el) categories.appendChild(el)
                        }
                    })
                }
                
                e.target.value = ""
            }
        })

        textarea.addEventListener("keyup", (e) => {

            let context = (e.target.value || "")
            if (context.startsWith(",")) e.target.value = context.substr(1)
            // else if (context.endsWith(",")) e.target.value = context.substr(0, context.length - 1)
            else e.target.value = context
        })
    })()
})
