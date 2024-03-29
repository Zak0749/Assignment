// The comments above functions as JSDoc
// This allows my IDE to know what types are coming in 
// This means faster development as I don't need to look at docs as my IDE knows all the properties

/**
 * Displays the search results on the page.
 * @param { HTMLFormElement } searchForm The search form element.
 */
async function displaySearchResults(searchForm) {
    // The results container
    let results = document.getElementById('search-results');

    // If search bar blank clear or too short do not give results
    if (searchForm.search.value.length < 3) {
        results.innerHTML = '';
    }
    // If the search bar has a answer
    else {
        // Set the url and search params
        let url = new URL('api/search', document.location);
        url.searchParams.set('search_string', searchForm.search.value);

        // Fetch the data from the api
        let response = await fetch(url);

        // Display result on page
        results.innerHTML = await response.text();
    }
}

/**
 * Logs in the user in
 *
 * @param {HTMLFormElement} loginForm The login form.
 */
async function loginUser(loginForm) {
    let body = new URLSearchParams();
    body.set("username", loginForm.username.value);
    body.set("password", loginForm.password.value);

    // Send a post request to the server to logins
    let result = await fetch('api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: body
    });

    // If login was successful 
    if (result.ok) {
        // Send user to their account
        window.location.replace("account");
    }
    // If it was an internal sever error
    else if (result.status == 500) {
        console.error(await result.text())
    }
    // When it is a client error (problem with inputs)
    else {
        // Get the error
        let error = await result.json();

        // Gets the element related to the error
        let input = loginForm[error.input];

        // Puts the servers custom message on it
        input.setCustomValidity(error.message)

        // Report the invalid input to the user
        input.reportValidity()
    }
}

/**
 * Checks if the repeat password matches the password field.
 *
 * @param {HTMLInputElement} passwordInput The repeat password input field.
 */
function checkPasswordsMatch(passwordInput) {
    // The form the repeat password box is in
    let form = passwordInput.form;

    // If the passwords match
    if (form["confirm-password"].value == form.password.value) {
        // Set field to be valid
        form["confirm-password"].setCustomValidity('')
    } else {
        // Invalidate the field with a message
        form["confirm-password"].setCustomValidity('passwords must match')
    }

    // Report the validity to the user when the form is submitted
    form["confirm-password"].checkValidity()
}

/**
 * Randomises the avatar
 * 
 * @param {HTMLButtonElement} avatarInput - the button containing the avatar
 */
function randomiseAvatar(avatarInput) {
    // Generate an number between 0 and 4294967295 (00000000 and ffffffff) then puts it into a hex string
    let seed = Math.floor(Math.random() * 4294967295).toString(16).padEnd(8, "0");

    avatarInput.style.backgroundImage = `url(https://api.dicebear.com/7.x/bottts/svg?backgroundColor=ffadad,ffd6a5,fdffb6,caffbf,9bf6ff,a0c4ff,bdb2ff,ffc6ff,fffffc&seed=${seed})`;
    avatarInput.value = seed;
}

/**
 * Creates an account for the user
 * 
 * @param {HTMLFormElement} createAccountForm - the create account from
 */
async function createAccount(createAccountForm) {
    let data = new URLSearchParams();
    data.set("username", createAccountForm.username.value)
    data.set("password", createAccountForm.password.value)
    data.set("avatar", createAccountForm.avatar.value)

    Array.from(createAccountForm.likes)
        .filter((checkbox) => checkbox.checked)
        .map((checkbox) => checkbox.value)
        .forEach((like, index) => {
            data.set(`likes[${index}]`, like)
        });

    let result = await fetch('api/create-account', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: data
    });

    if (result.ok) {
        // Send user to their account
        window.location.replace("account");
    } else {
        let body = await result.json()

        let input = createAccountForm[body['input']]

        input.setCustomValidity(body.message)

        input.reportValidity()
    }
}

/**
 * @param {HTMLButtonElement} toggleButton - the create account from
 */
async function toggleSave(toggleButton) {
    let data = new URLSearchParams(window.location.search);

    if (toggleButton.dataset.save) {
        let result = await fetch('api/delete-save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: data
        });

        if (result.ok) {
            toggleButton.children[0].innerHTML = 'bookmark_add';
            toggleButton.dataset.save = true
        } else {
            console.error('Unexpected server error');
        }
    } else {
        let result = await fetch('api/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: data
        });

        if (result.ok) {
            toggleButton.children[0].innerHTML = 'bookmark_added';
            toggleButton.dataset.save = false
        } else {
            console.error('Unexpected server error');
        }
    }
}

async function logout() {
    let request = await fetch('api/logout');

    if (request.ok) {
        window.location.replace('/')
    } else {
        console.error(await request.text())
    }
}

/**
 * @param {HTMLFormElement} editAccountForm - the edit deck form
 */
async function submitEditAccount(editAccountForm) {
    let data = new URLSearchParams();

    if (editAccountForm['username'].value != editAccountForm['username'].defaultanswer) {
        data.set("username", editAccountForm.username.value)
    }

    if (editAccountForm['password'].value != editAccountForm['password'].defaultanswer) {
        data.set("password", editAccountForm.password.value)
    }

    if (editAccountForm['avatar'].value != editAccountForm['avatar'].defaultanswer) {
        data.set("avatar", editAccountForm.avatar.value)
    }

    Array.from(editAccountForm.likes)
        .filter((checkbox) => checkbox.checked != checkbox.defaultChecked && checkbox.checked == true)
        .map((checkbox) => checkbox.value)
        .forEach((like, index) => {
            data.set(`added_likes[${index}]`, like)
        });

    Array.from(editAccountForm.likes)
        .filter((checkbox) => checkbox.checked != checkbox.defaultChecked && checkbox.checked == false)
        .map((checkbox) => checkbox.value)
        .forEach((like, index) => {
            data.set(`removed_likes[${index}]`, like)
        });

    let response = await fetch('/api/edit-account', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: data
    });

    if (response.ok) {
        window.location.replace('account')
    } else if (response.status == 500) {
        console.error(await response.text())
    }
    // When it is a client error (problem with inputs)
    else {
        // Get the error
        let error = await response.json();

        // Gets the element related to the error
        let input = editAccountForm[error.input];

        // Puts the servers custom message on it
        input.setCustomValidity(error.message)

        // Report the invalid input to the user
        input.reportValidity()
    }
}

function open_dialog(id) {
    document.getElementById(id).showModal();
}

function close_dialog(id) {
    document.getElementById(id).close();
}

// Problem solve -> get coordinates of click had to used onload with event listener

// When the page loads add an on click event to all dialog's so when the user clicks outside the dialog the dialog closes
window.addEventListener('load', () => {
    // Foreach dialog element
    Array.from(document.getElementsByTagName('dialog')).forEach((dialog) => {
        dialog.addEventListener('click', (click) => {
            // Gets the actual size of the dialog
            const size = click.target.getBoundingClientRect();

            // If clicked outside the dialog close it and the dialog is open
            if ((size.top > click.clientY || click.clientY > size.top + size.height || size.left > click.clientX || click.clientX > size.left + size.width) && click.target.open == true) {
                click.target.close();
            }
        });
    });
});


async function delete_account() {
    let response = await fetch('api/delete-account', {
        method: 'POST'
    });

    if (response.ok) {
        window.location.replace('/');
    } else {
        console.error(await response.text());
    }
}

/**
 *  * @param {HTMLButtonElement} id - the edit deck form
 * @param {string} id - the edit deck form
 */
function changeTab(button, id) {
    let old_tab = document.getElementsByClassName('selected-tab')[0];
    let old_button = document.getElementsByClassName('selected-tab-button')[0];

    old_tab.classList.remove('selected-tab');
    old_button.classList.remove('selected-tab-button');

    let new_tag = document.getElementById(id);

    new_tag.classList.add('selected-tab')
    button.classList.add('selected-tab-button')
}

/**
 *  * @param {HTMLTextAreaElement} textArea - the edit deck form
 */
function autoHeight(textArea) {
    textArea.style.height = 'auto';
    textArea.style.height = textArea.scrollHeight + 'px';
}

function cardModeEdit() {
    document.getElementById('card-tab').dataset.mode = 'edit';
}

function cardModeDelete() {
    document.getElementById('card-tab').dataset.mode = 'delete';
}

function addCard() {
    // Get the list of elements from the DOM
    let cardList = document.getElementById("card-edit-list");

    // Creates a list element
    let newCard = document.createElement("li");

    // Insert the html
    newCard.innerHTML = `
        <fieldset class="card-fieldset" name="cards">
            <div class="card-editor form-field" oninput="matchHeights(this)">
                <textarea placeholder="question" name="question" class="card-question" required="" maxlength="128" oninvalid="changeTab(document.getElementById('card-tab-button'),'card-tab')"></textarea>
                <textarea placeholder="answer" name="answer" class="card-answer" required="" maxlength="256" oninvalid="changeTab(document.getElementById('card-tab-button'),'card-tab')"></textarea>
            </div>
            <button class="card-delete-button" type="button" onclick="removeCard(this)">
                <span class="material-symbols-outlined">
                    delete
                </span>
            </button>
        </fieldset>
    `;

    // Add it to the card list
    cardList.appendChild(newCard);

    // Increment the counter
    document.getElementById('card-counter').value = Number(document.getElementById('card-counter').value) + 1;
}

/**
 *  * @param {HTMLButtonElement} button - the edit deck form
 */
function removeCard(button) {
    // Gets the form field element associated with the button
    let card = button.parentElement;

    // Marks it to be deleted
    card.dataset.remove = true;

    // Hides it
    card.setAttribute('disabled', '');

    // Removes one from the counter
    document.getElementById('card-counter').value -= 1;
}

/**
 *  * @param {HTMLButtonElement} button - the edit deck form
 */
function undoDeletions() {
    // Find all the elements marked to be removed
    let removed = Array.from(document.querySelectorAll('[data-remove="true"]'))

    // Loop over each element that was going to be deleted
    removed.forEach((card) => {
        // Mark it not to be deleted
        card.dataset.remove = false;

        // Show the card editor
        card.removeAttribute('disabled');
    })

    // Add the number of items brought back to the question counter
    document.getElementById('card-counter').value = removed.length + Number(document.getElementById('card-counter').value);
}


/**
 *  * @param {HTMLDivElement} button - the edit deck form
 */
function matchHeights(button) {

    // Get both the text areas
    let [question_input, answer_input] = button.children;

    // On small screens the boxes are vertically stacked so they both need to be done individually 
    if (window.innerWidth < 600) {
        // Have their heights calculated only based on themselves
        autoHeight(question_input);
        autoHeight(answer_input);
    } else {
        // Required to get the height of the text
        question_input.style.height = 'auto';
        answer_input.style.height = 'auto';

        // Find the tallest of the two boxes
        let max = Math.max(question_input.scrollHeight, answer_input.scrollHeight);

        // Set both inputs heights to the largest one
        question_input.style.height = max + 'px';
        answer_input.style.height = max + 'px';
    }
}

/**
 *  * @param {HTMLFormElement} createDeckForm - the edit deck form
 */
async function createDeck(createDeckForm) {
    // Enstatite data structure to send data to server
    let data = new URLSearchParams();

    // Add the title and description to be sent to the server
    data.set("title", createDeckForm['title'].value);
    data.set("description", createDeckForm['description'].value);

    // Get all the topics elements
    Array.from(createDeckForm.topics)
        .filter((checkbox) => checkbox.checked) // Filter down to all selected tags
        .forEach((checkbox, index) => { // For each checkbox add it's id to be sent to the server
            data.set(`topics[${index}]`, checkbox.value)
        });

    // Get all the card elements
    Array.from(createDeckForm.cards)
        .filter((card) => card.dataset.remove !== 'true') // Filter out the elements that have been deleted
        .forEach((card, index) => { // For each editor add the data to be sent to the server
            data.set(`cards[${index}][question]`, card.getElementsByClassName('card-question')[0].value)
            data.set(`cards[${index}][answer]`, card.getElementsByClassName('card-answer')[0].value)
        })

    // Send the request to the server
    let result = await fetch('api/create-deck', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: data
    });

    // If request was successful 
    if (result.ok) {
        // Get the newly created deck_id
        // let deck_id = await result.text();

        // Redirect to the newly created deck's page
        // window.location.replace(`deck?deck_id=${deck_id}`)

        console.log(await result.text())
    } else {
        // Send a major error to the user
        alert("There was an error trying to submit this form please try again later")
    }
}

/**
 *  * @param {HTMLFormElement} form - the edit deck form
 */
async function submitEditDeck(editDeckForm) {
    // Enstatite data structure to send data to server
    let data = new URLSearchParams();

    // Add the deck_id from the current page to send to the server
    data.set("deck_id", (new URLSearchParams(window.location.search)).get("deck_id"));

    // If the title has changed
    if (editDeckForm.title.value !== editDeckForm.title.defaultValue) {
        // Add the title to be send to the server
        data.set("title", editDeckForm['title'].value);
    }

    // If the description has changed
    if (editDeckForm.description.value !== editDeckForm.description.defaultValue) {
        // Add the description to be send to the server
        data.set("description", editDeckForm.description.value);
    }

    // Get the list of topics
    let topics = Array.from(editDeckForm.topics)


    topics
        .filter((checkbox) => checkbox.checked != checkbox.defaultChecked && checkbox.checked == true) // Find the topics that are new
        .forEach((checkbox, index) => { // For each new topic add to be sent to the server
            data.set(`added_topics[${index}]`, checkbox.value)
        });

    topics
        .filter((checkbox) => checkbox.checked != checkbox.defaultChecked && checkbox.checked == false) // Find the topics that were set but are no longer
        .forEach((checkbox, index) => { // For each removed topic add to be sent to the server
            data.set(`removed_topics[${index}]`, checkbox.value)
        });

    // Get the list of cards
    let cards = Array.from(editDeckForm.cards);

    cards
        .filter((card) => card.dataset.remove !== "true" && card.id === "") // Find all the new card
        .forEach((card, index) => { // For each new card add to be sent to the server
            data.set(`new_cards[${index}][question]`, card.getElementsByClassName('card-question')[0].value)
            data.set(`new_cards[${index}][answer]`, card.getElementsByClassName('card-answer')[0].value)
        });

    cards
        .filter((card) => card.dataset.remove !== "true" && card.id !== "") // Filter out cards that have been removed or are new
        .map((card) => { // Tidy up fields 
            return {
                id: card.id,
                question: card.getElementsByClassName('card-question')[0],
                answer: card.getElementsByClassName('card-answer')[0]
            }
        })
        .filter((card) => card.question.value !== card.question.defaultValue || card.answer.value !== card.answer.defaultValue) // Find cards that have been changed 
        .forEach((card, index) => { // For each changed card
            // Add the edited card's id to be sent to the server
            data.set(`edited_cards[${index}][card_id]`, card.id)

            // If the cards question has been changed add it to be sent to the server
            if (card.question.value !== card.question.defaultValue) {
                data.set(`edited_cards[${index}][question]`, card.question.value)
            }

            // If the cards answer has been changed add it to be sent to the server
            if (card.answer.value !== card.answer.defaultValue) {
                data.set(`edited_cards[${index}][answer]`, card.answer.value)
            }
        });

    cards
        .filter((question) => question.dataset.remove === "true" && question.id !== "") // Find all the previously created cards to be deleted
        .forEach((question, index) => { // For each deleted card mark down its id to be deleted
            data.set(`removed_cards[${index}]`, question.id)
        });

    // Send the data to the server
    let response = await fetch('/api/edit-deck', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: data
    });

    // If the edit was a success 
    if (response.ok) {
        // Redirect to see the changes 
        window.location.replace(`deck?deck_id=${data.get("deck_id")}`);
    } else {
        // Send a error to the user
        alert("there was an error editing this deck please try again later");
    }
}

async function deleteDeck() {
     // Enstatite data structure to send data to server
     let data = new URLSearchParams();

     // Add the deck_id from the current page to send to the server
     data.set("deck_id", (new URLSearchParams(window.location.search)).get("deck_id"));

    // Send the data to the server
    let response = await fetch('/api/delete-deck', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: data
    });

    // If the edit was a success 
    if (response.ok) {
        // Redirect to the account page
        window.location.replace(`account`)
    } else {
         // Send a error to the user
         alert("there was an error deleting this deck please try again later");
    }
}

/**
 *  * @param {HTMLButtonElement} button - the edit deck form
 */
function selectAnswer(button) {
    // Get the first play-question witch is a ancestor of the button
    let questionElement = button.closest('.play-question');

    console.log("selected", questionElement)

    // If question has already been answered stop function early
    if (questionElement.dataset.result) {
        return
    }

    if (questionElement.dataset.correctId == button.dataset.answerId) {
        questionElement.dataset.result = 'correct'
    } else {
        questionElement.dataset.result = 'wrong'
    }
}

/**
 *  * @param {HTMLButtonElement} button - the edit deck form
 */
function matchAnswer(button) {
    // Get the first play-question witch is a ancestor of the button
    let questionElement = button.closest('.play-question');

    if (questionElement.dataset.result) {
        return;
    }

    if (button.dataset.selected == true) {
        button.dataset.selected = false;
        return;
    }

    let selected = questionElement.querySelector('[data-selected="true"]')


    if (selected == null) {
        button.dataset.selected = true;
    } else if (button.parentElement.isSameNode(selected.parentElement)) {
        button.dataset.selected = true;
        selected.dataset.selected = false;
    } else {
        if (selected.dataset.questionId == button.dataset.questionId) {
            // If any questions left
            selected.setAttribute('disabled', '')
            selected.dataset.selected = false;
            button.setAttribute('disabled', '')

            // If done
            if (questionElement.querySelectorAll('button:disabled').length == 8) {
                questionElement.dataset.result = 'correct';
            }
        } else {
            questionElement.dataset.result = 'wrong';
        }
    }
}

/**
 *  * @param {HTMLButtonElement} button - the edit deck form
 */
function nextQuestion(button) {
    // document.body.onbeforeunload = () => {
    //     return 'Your changes may not be saved, are you sure you want to leave?'
    // };


    // Get the first play-question witch is a ancestor of the button
    let questionElement = button.closest('.play-question');

    // This means it is the rety poage so just return
    if (questionElement === null) {
        let retryPage = button.closest('.retry-page');
        retryPage.dataset.complete = true;
        return;
    }

    questionElement.dataset.complete = true;

    // If last question


    // If the retry page



    if (questionElement.dataset.result == 'correct') {
        document.getElementById('play-progress').value += 1;
    } else {
        let duplicatedQuestion = questionElement.cloneNode(true);

        duplicatedQuestion.removeAttribute('data-result');
        duplicatedQuestion.removeAttribute('data-complete');
        duplicatedQuestion.removeAttribute('data-original');
        duplicatedQuestion.classList.remove('revealed');

        let questionList = questionElement.parentElement;

        questionList.appendChild(duplicatedQuestion);
    }

    if (questionElement.nextElementSibling == null || document.getElementById('round').lastElementChild.classList.contains('retry-page') && questionElement.nextElementSibling.classList.contains('retry-page')) {
        results();
    }
}

/**
 *  * @param {HTMLButtonElement} button - the edit deck form
 */
function displaySelfAnswer(button) {
    let questionElement = button.closest('.play-question');

    questionElement.classList.add('revealed');
}

/**f
 *  * @param {HTMLButtonElement} button - the edit deck form
 *  *  @param {string} button - the edit deck form

 */
function selfAnswer(button, result) {
    let questionElement = button.closest('.play-question');

    questionElement.dataset.result = result;
}

let addedUnLoadCheck = false;

function results() {
    // API STUFF


    let questionList = document.getElementById('round')
    questionList.style.display = 'none';
    document.getElementById('results').style.display = 'block';

    // Chart

    let questions = Array.from(questionList.children)

    let markable_questions = questions.filter((question) => question.dataset.original === "true");

    questions.forEach((q) => {
        console.log(q.dataset.original);
    })

    let correct_number = markable_questions.filter((question) => question.dataset.result == 'correct').length;
    let wrong_number = markable_questions.filter((question) => question.dataset.result == 'wrong').length;

    saveResults(correct_number);

    const ctx = document.getElementById('results-chart');

    document.getElementById('correct-number').innerText = correct_number
    document.getElementById('wrong-number').innerText = wrong_number


    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [
                'Correct',
                'Wrong',
            ],
            datasets: [{
                data: [correct_number, wrong_number],
                backgroundColor: [
                    getComputedStyle(document.body).getPropertyanswer('--accent'),
                    getComputedStyle(document.body).getPropertyanswer('--secondary-background')
                ],
                hoverBackgroundColor: [
                    getComputedStyle(document.body).getPropertyanswer('--accent-hover'),
                    getComputedStyle(document.body).getPropertyanswer('--secondary-background-hover')
                ],
                hoverOffset: 4,
                borderWidth: 0,
            }]
        },
        options: {
            layout: {
                padding: 5
            },
            plugins: {
                legend: {
                    display: false
                },
            }
        }
    });


    // Table

    let tableBody = document.getElementById('results-table-body');

    questions.forEach((question) => {
        if (!question.classList.contains('retry-page')) {
            let row = document.createElement('tr');

            let numberElement = document.createElement('td');

            numberElement.innerText = question.dataset.index;

            let questionElement = document.createElement('td');

            questionElement.innerText = question.dataset.questionText;

            let resultElement = document.createElement('td');

            resultElement.innerText = question.dataset.result;

            row.appendChild(numberElement);
            row.appendChild(questionElement);
            row.appendChild(resultElement);


            tableBody.appendChild(row);
        }
    })
}

/**
 *  * @param {integer} score - the edit deck form
 * 
 */
async function saveResults(score) {
    let data = new URLSearchParams(window.location.search);

    data.set("score", score)


    let result = await fetch('/api/play', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: data
    });

    if (!result.ok) {
        console.error(await result.text())
    } else {
        // REMOVE LATER
        console.log('error')
    }
}

/*
    Since all shortcuts are simply an interface on top of the 

*/
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

    // Calls the on load function for all elements with the onload property set
    Array.from(document.querySelectorAll("[onload]")).forEach((textarea) => textarea.onload())
})
