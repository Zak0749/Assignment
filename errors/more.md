```
let seed = Math.floor(Math.random() * 4294967295).toString(16);
```

when generating seed it would someitmes give validation errors as less than 8 letters as if under threshld the zero will be cut off

added

padStart(8, "0"); 

which ensures that the length is 8 characters otherwise inserts zeros if there a missing zero

had to add ROUND to ge tthe score as it was crazy

# IN PLAY KEYBOARD SHORTCUTS WOULD ONLY TARGET LAST QUESTION AND WOULD FORGET

window.addEventListener("load", () => {
    let shortcutItems = {}
    // Iterates through all the elements on the page with the ks-bind attribute
    Array.from(document.querySelectorAll("[keyboard-shortcut]")).forEach((element) => {
         Mousetrap.bind(element.getAttribute("keyboard-shortcut")., () => {
            element.click
        });
    })
})
    
SO does muliple 

window.addEventListener("load", () => {
    let shortcutItems = {}
    // Iterates through all the elements on the page with the ks-bind attribute
    Array.from(document.querySelectorAll("[keyboard-shortcut]")).forEach((element) => {
        // Adds a key bind to the element with the name specified to do it's click action
        // e.g. a button will do it's onclick event a tag will redirect etc
        if (shortcutItems[element.getAttribute("keyboard-shortcut")]) {
            shortcutItems[element.getAttribute("keyboard-shortcut")].push(element)
        } else {
            shortcutItems[element.getAttribute("keyboard-shortcut")] = [element]
        }


    })

    Object.entries(shortcutItems).forEach(([key, elements]) => {
        Mousetrap.bind(key, () => {
            elements.forEach((element) => {
                    element.click()
            })
        });
    })
})

BUT THEN WOULD ANSWER MULTIPLE AT ONCE so check if element visible

window.addEventListener("load", () => {
    let shortcutItems = {}
    // Iterates through all the elements on the page with the ks-bind attribute
    Array.from(document.querySelectorAll("[keyboard-shortcut]")).forEach((element) => {
        // Adds a key bind to the element with the name specified to do it's click action
        // e.g. a button will do it's onclick event a tag will redirect etc
        if (shortcutItems[element.getAttribute("keyboard-shortcut")]) {
            shortcutItems[element.getAttribute("keyboard-shortcut")].push(element)
        } else {
            shortcutItems[element.getAttribute("keyboard-shortcut")] = [element]
        }


    })

    Object.entries(shortcutItems).forEach(([key, elements]) => {
        Mousetrap.bind(key, () => {
            elements.forEach((element) => {
                // If element is visible
                if (element.offsetParent !== null) {
                    element.click()
                }
            })
        });
    })
})
