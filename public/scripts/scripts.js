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
    // If the search bar has a value
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
        window.location.replace('my-account');
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
        window.location.replace('my-account')
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

    if (editAccountForm['username'].value != editAccountForm['username'].defaultValue) {
        data.set("username", editAccountForm.username.value)
    }

    if (editAccountForm['password'].value != editAccountForm['password'].defaultValue) {
        data.set("password", editAccountForm.password.value)
    }

    if (editAccountForm['avatar'].value != editAccountForm['avatar'].defaultValue) {
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
        window.location.replace('my-account')
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

function question_mode_edit() {
    document.getElementById('question-tab').dataset.mode = 'edit';
}

function question_mode_delete() {
    document.getElementById('question-tab').dataset.mode = 'delete';
}

function addQuestion() {
    // TODo replace with validated etc

    let questionList = document.getElementById('question-list');

    let newQuestion = questionList.firstElementChild.cloneNode(true);

    newQuestion.children[0].removeAttribute('id');
    newQuestion.getElementsByTagName('textarea')[0].value = '';
    newQuestion.getElementsByTagName('textarea')[1].value = '';

    questionList.appendChild(newQuestion);

    document.getElementById('question-counter').value = 1 + parseInt(document.getElementById('question-counter').value);
}

/**
 *  * @param {HTMLButtonElement} button - the edit deck form
 */
function removeQuestion(button) {
    let question = button.parentElement;

    question.dataset.remove = true;
    question.setAttribute('disabled', '');

    document.getElementById('question-counter').value -= 1;
}

/**
 *  * @param {HTMLButtonElement} button - the edit deck form
 */
function undoDeletions() {
    let removed = Array.from(document.querySelectorAll('[data-remove="true"]'))
    removed.forEach((question) => {
        question.dataset.remove = false;
        question.removeAttribute('disabled');
    })

    document.getElementById('question-counter').value = removed.length + parseInt(document.getElementById('question-counter').value);
}


/**
 *  * @param {HTMLDivElement} button - the edit deck form
 */
function matchHeights(button) {
    // On small screens the boxes are vertically stacked so no moving is needed
    if (window.innerWidth < 600) {
        return
    }


    let [key_input, value_input] = button.children;

    key_input.style.height = 'auto';
    value_input.style.height = 'auto';

    let max = Math.max(key_input.scrollHeight, value_input.scrollHeight);

    key_input.style.height = max + 'px';
    value_input.style.height = max + 'px';

}

/**
 *  * @param {HTMLFormElement} createDeckForm - the edit deck form
 */
async function createDeck(createDeckForm) {
    let data = new URLSearchParams();

    data.set("title", createDeckForm['title'].value);
    data.set("description", createDeckForm['description'].value);

    Array.from(createDeckForm.topics)
        .filter((checkbox) => checkbox.checked)
        .map((checkbox) => checkbox.value)
        .forEach((like, index) => {
            data.set(`topics[${index}]`, like)
        });

    Array.from(createDeckForm.getElementsByClassName('question'))
        .filter((question) => question.dataset.remove !== 'true')
        .forEach((question, index) => {
            data.set(`questions[${index}][key]`, question.getElementsByClassName('question-key')[0].value)
            data.set(`questions[${index}][value]`, question.getElementsByClassName('question-value')[0].value)
        })


    let result = await fetch('api/create-deck', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: data
    });

    if (result.ok) {
        let deck_id = await result.text();
        window.location.replace(`deck?deck_id=${deck_id}`)
    } else {
        console.log('ERROR:', await result.text())
    }
}

/**
 *  * @param {HTMLFormElement} form - the edit deck form
 */
async function submitEditDeck(editDeckForm) {
    let data = new URLSearchParams(window.location.search);

    if (editDeckForm.title.value !== editDeckForm.title.defaultValue) {
        data.set("title", editDeckForm['title'].value);
    }

    if (editDeckForm.description.value !== editDeckForm.description.defaultValue) {
        data.set("description", editDeckForm.description.value);
    }

    let topics = Array.from(editDeckForm.topics)

    topics
        .filter((checkbox) => checkbox.checked != checkbox.defaultChecked && checkbox.checked == true)
        .forEach((checkbox, index) => {
            data.set(`added_topics[${index}]`, checkbox.value)
        });

    topics
        .filter((checkbox) => checkbox.checked != checkbox.defaultChecked && checkbox.checked == false)
        .forEach((checkbox, index) => {
            data.set(`removed_topics[${index}]`, checkbox.value)
        });

    let questions = Array.from(document.getElementsByClassName('question'));

    questions
        .filter((question) => question.dataset.remove !== "true" && question.id === "")
        .forEach((question, index) => {
            data.set(`new_questions[${index}][key]`, question.getElementsByClassName('question-key')[0].value)
            data.set(`new_questions[${index}][value]`, question.getElementsByClassName('question-value')[0].value)
        });

    questions
        .filter((question) => question.dataset.remove !== "true" && question.id !== "")
        .map((question) => {
            return {
                id: question.id,
                key: question.getElementsByClassName('question-key')[0],
                value: question.getElementsByClassName('question-value')[0]
            }
        })
        .filter((question) => question.key.value !== question.key.defaultValue || question.value.value !== question.value.defaultValue)
        .forEach((question, index) => {
            data.set(`edited_questions[${index}][id]`, question.id)
            if (question.key.value !== question.key.defaultValue) {
                data.set(`edited_questions[${index}][key]`, question.key.value)
            }
            if (question.value.value !== question.value.defaultValue) {
                data.set(`edited_questions[${index}][value]`, question.value.value)
            }
        });

    questions
        .filter((question) => question.dataset.remove === "true" && question.id !== "")
        .forEach((question, index) => {
            data.set(`removed_questions[${index}][id]`, question.id)
        });

    let response = await fetch('/api/edit-deck', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: data
    });

    if (response.ok) {
        window.location.replace(`deck?deck_id=${data.deck_id}`)
    } else {
        console.error(await response.text())
    }
}

async function deleteDeck() {
    let data = new URLSearchParams(window.location.search);

    let response = await fetch('/api/delete-deck', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: data
    });

    if (response.ok) {
        window.location.replace(`my-account`)
    } else {
        console.error(await response.text())
    }
}

/**
 *  * @param {HTMLButtonElement} button - the edit deck form
 */
function selectAnswer(button) {
    // Get the first play-question witch is a ancestor of the button
    let questionElement = button.closest('.play-question');

    // If question has already been answered stop function early
    if (questionElement.dataset.result) {
        return
    }

    questionElement.dataset.result = questionElement.dataset.correctId == button.dataset.answerId ? 'correct' : 'wrong';
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

    if (questionElement.nextElementSibling == null || document.getElementById('play-question-list').lastElementChild.classList.contains('retry-page') && questionElement.nextElementSibling.classList.contains('retry-page')) {
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


    let questionList = document.getElementById('play-question-list')
    questionList.style.display = 'none';
    document.getElementById('play-results').style.display = 'block';

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
                    getComputedStyle(document.body).getPropertyValue('--accent'),
                    getComputedStyle(document.body).getPropertyValue('--secondary-background')
                ],
                hoverBackgroundColor: [
                    getComputedStyle(document.body).getPropertyValue('--accent-hover'),
                    getComputedStyle(document.body).getPropertyValue('--secondary-background-hover')
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
})
