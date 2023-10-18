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
    // Send a post request to the server to logins
    let result = await fetch('api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            username: loginForm.username.value,
            password: loginForm.password.value
        })
    });

    // If login was successful 
    if (result.ok) {
        // Send user to their account
        // window.location.replace('my-account');
    }
    // If it was an internal sever error
    else if (result.status == 500) {
        loginForm.getElementById('error-text').innerText = 'An internal server error has occurred please try again later';
    }
    // When it is a client error (problem with inputs)
    else {
        // Get the error
        let error = await result.json();

        // Gets the element related to the error
        let input = form[error.input];

        // Puts the servers custom message on it
        input.setCustomValidity(error.message)

        // Report the invalid input to the user
        input.reportValidity()
    }
}

/**
 * Checks if the repeat password matches the password field.
 *
 * @param {HTMLInputElement} repeatPassword The repeat password input field.
 */
function checkPasswordsMatch(repeatPassword) {
    // The form the repeat password box is in
    let form = repeatPassword.form;

    // If the passwords match
    if (repeatPassword.value == form.password.value) {
        // Set field to be valid
        repeatPassword.setCustomValidity('')
    } else {
        // Invalidate the field with a message
        repeatPassword.setCustomValidity('passwords must match')
    }

    // Report the validity to the user when the form is submitted
    repeatPassword.checkValidity()
}

/**
 * Randomises the avatar
 * 
 * @param {HTMLButtonElement} avatarInput - the button containing the avatar
 */
function randomiseAvatar(avatarInput) {
    // Generate an number between 0 and 4294967295 (00000000 and ffffffff) then puts it into a hex string
    let seed = Math.floor(Math.random() * 4294967295).toString(16);

    avatarInput.style.backgroundImage = `url(https://api.dicebear.com/7.x/bottts/svg?backgroundColor=ffadad,ffd6a5,fdffb6,caffbf,9bf6ff,a0c4ff,bdb2ff,ffc6ff,fffffc&seed=${seed})`;
    avatarInput.value = seed;
}

/**
 * Creates an account for the user
 * 
 * @param {HTMLFormElement} createAccountForm - the create account from
 */
async function createAccount(createAccountForm) {
    let data = new URLSearchParams({
        username: createAccountForm['username'].value,
        password: createAccountForm['password'].value,
        avatar: createAccountForm['avatar'].value,
        likes: Array.from(createAccountForm['likes'])
            .filter((checkbox) => checkbox.checked) // Get only the checkboxes which are checked
            .map((checkbox) => checkbox.value) // Get the id of the tag
    });

    // data.set("username", createAccountForm.username.value);
    // data.set("password", createAccountForm.password.value);
    // data.set("avatar", createAccountForm.avatar.value);
    // data.set("likes", Array.from(createAccountForm['likes'])
    //     .filter((checkbox) => checkbox.checked) // Get only the checkboxes which are checked
    //     .map((checkbox) => checkbox.value)) // Get the id of the tag)

    let result = await fetch('api/create-account', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: data
        // body: JSON.stringify({
        //     username: form['username'].value,
        //     password: form['password'].value,
        //     avatar: form['avatar'].value,
        // likes: Array.from(form['likes'])
        //     .filter((checkbox) => checkbox.checked) // Get only the checkboxes which are checked
        //     .map((checkbox) => checkbox.value) // Get the id of the tag
        // })
    });

    // Think about adding 500 error as unknown server error
    if (result.ok) {
        console.log(await result.text())
        window.location.replace('my-account')
    } else {
        let body = await result.json()

        let input = form[body['input']]

        input.setCustomValidity(body.message)

        input.reportValidity()
    }
}

async function logout() {
    let result = await fetch('api/logout');

    if (result.ok) {
        window.location.replace('/');
    } else {
        console.error('Unexpected server error');
    }
}

/**
 * @param {HTMLButtonElement} button - the create account from
 */
async function toggle_save(button) {
    if (button.dataset.save) {
        let result = await fetch('api/delete-save?deck_id=' + button.dataset.deckId, {
            method: 'PATCH',
        });

        if (result.ok) {
            button.children[0].innerHTML = 'bookmark_add';
            button.dataset.save = true
        } else {
            console.error('Unexpected server error');
        }
    } else {
        let result = await fetch('api/save?deck_id=' + button.dataset.deckId, {
            method: 'PATCH',
        });

        if (result.ok) {
            button.children[0].innerHTML = 'bookmark_added';
            button.dataset.save = false
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
 * @param {HTMLFormElement} form - the edit deck form
 */
async function submit_edit_account(form) {
    let changed_likes = Array.from(form['likes']).filter((v) => v.checked != v.defaultChecked);

    let URLPARAMS = URLSearchParams({
        username: form['username'].value == form['username'].defaultValue ? null : form['username'].value,
        password: form['password'].value == form['password'].defaultValue ? null : form['password'].value,
        avatar: form['avatar'].value == form['avatar'].defaultValue ? null : form['avatar'].value,
        added_likes: changed_likes.filter((v) => v.checked).map((v) => v.value),
        removed_likes: changed_likes.filter((v) => !v.checked).map((v) => v.value)
    })

    let response = await fetch('/api/edit-account', {
        method: 'PATCH', // Since editing and not setting all fields the PATCH HTTP METHOD IS USED
        body: URLPARAMS
    });

    if (response.ok) {
        window.location.replace('my-account')
    } else {
        console.error(await response.text())
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
        method: 'DELETE'
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
    let [key_input, value_input] = button.children;

    key_input.style.height = 'auto';
    value_input.style.height = 'auto';

    let max = Math.max(key_input.scrollHeight, value_input.scrollHeight);

    key_input.style.height = max + 'px';
    value_input.style.height = max + 'px';

}

/**
 *  * @param {HTMLFormElement} form - the edit deck form
 */
async function createDeck(form) {
    let result = await fetch('api/create-deck', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            title: form['title'].value,
            description: form['description'].value,
            topics: Array.from(form['topics'])
                .filter((checkbox) => checkbox.checked) // Get only the checkboxes which are checked
                .map((checkbox) => checkbox.value), // Get the id of the tag
            questions: Array.from(form.getElementsByClassName('question'))
                .filter((question) => question.dataset.remove !== 'true')
                .map((question) => {
                    return {
                        key: question.getElementsByClassName('question-key')[0].value,
                        value: question.getElementsByClassName('question-value')[0].value,
                    }
                })
        })
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
async function submitEditDeck(form) {
    let changed_topics = Array.from(form['likes']).filter((v) => v.checked != v.defaultChecked);
    let questions = Array.from(document.getElementsByClassName('question'));

    console.log(questions)

    let data = {
        deck_id: document.getElementsByTagName('form')[0].dataset.deckId,
        title: form['title'].value == form['title'].defaultValue ? null : form['title'].value,
        description: form['description'].value == form['description'].defaultValue ? null : form['description'].value,
        added_topics: changed_topics.filter((v) => v.checked).map((v) => v.value),
        removed_topics: changed_topics.filter((v) => !v.checked).map((v) => v.value),
        new_questions: questions.filter((question) => question.dataset.remove !== 'true').filter((v) => v.id === '').map((question) => {
            return {
                key: question.getElementsByClassName('question-key')[0].value,
                value: question.getElementsByClassName('question-value')[0].value,
            }
        }),
        edited_questions: questions.filter((question) => question.dataset.remove !== 'true').filter((v) => v.id !== '').filter((question) => {
            let key = question.getElementsByClassName('question-key')[0];
            let value = question.getElementsByClassName('question-value')[0];

            return key.value !== key.defaultValue || value.value !== value.defaultValue;
        }).map((question) => {
            return {
                id: question.id,
                key: question.getElementsByClassName('question-key')[0].value == question.getElementsByClassName('question-key')[0].defaultValue ? null : question.getElementsByClassName('question-key')[0].value,
                value: question.getElementsByClassName('question-value')[0].value == question.getElementsByClassName('question-value')[0].defaultValue ? null : question.getElementsByClassName('question-value')[0].value,
            }
        }),
        // rather than actually delete add data-delete=true 
        // allow undo edits + give id list of deletions
        removed_questions: questions.filter((v) => v.id !== '').filter((question) => question.dataset.remove === 'true').map((question) => question.id)
    }

    let response = await fetch('/api/edit_deck', {
        method: 'PATCH', // Since editing and not setting all fields the PATCH HTTP METHOD IS USED
        body: JSON.stringify(data)
    });

    if (response.ok) {
        window.location.replace(`deck?deck_id=${data.deck_id}`)
    } else {
        console.error(await response.text())
    }
}

async function deleteDeck() {
    let deckId = document.getElementsByTagName('form')[0].dataset.deckId;

    let response = await fetch('/api/delete-deck', {
        method: 'DELETE',
        body: JSON.stringify({
            deck_id: deckId
        })
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
    if (questionElement.dataset.dataResult) {
        return
    }

    questionElement.dataset.result = questionElement.dataset.correctId == button.dataset.answerId ? 'correct' : 'wrong';
}

/**
 *  * @param {HTMLButtonElement} button - the edit deck form
 */
function matchAnswer(button) {
    if (button.dataset.selected == true) {
        button.dataset.selected = false;
        return;
    }

    // Get the first play-question witch is a ancestor of the button
    let questionElement = button.closest('.play-question');

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
    document.body.onbeforeunload = () => {
        return 'Your changes may not be saved, are you sure you want to leave?'
    };


    // Get the first play-question witch is a ancestor of the button
    let questionElement = button.closest('.play-question');

    questionElement.dataset.complete = true;

    // If last question


    // If the retry page

    if (questionElement.classList.contains('retry-page')) {
        return;
    }

    if (questionElement.dataset.result == 'correct') {
        document.getElementById('play-progress').value += 1;
    } else {
        let duplicatedQuestion = questionElement.cloneNode(true);

        duplicatedQuestion.removeAttribute('data-result');
        duplicatedQuestion.removeAttribute('data-complete');
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

/**
 *  * @param {HTMLButtonElement} button - the edit deck form
 *  *  @param {string} button - the edit deck form

 */
function selfAnswer(button, result) {
    let questionElement = button.closest('.play-question');

    questionElement.dataset.result = result;
}

let addedUnLoadCheck = false;

// When submitting doesn't allow either
function contentChanged() {
    if (addedUnLoadCheck == false) {
        addedUnLoadCheck = true
        document.body.onbeforeunload = () => {
            return 'Your changes may not be saved, are you sure you want to leave?'
        };

    }
}


function results() {
    // API STUFF


    let questionList = document.getElementById('play-question-list')
    questionList.style.display = 'none';
    document.getElementById('play-results').style.display = 'block';

    // Chart

    let questions = Array.from(questionList.children)

    let correct_number = questions.slice(0, 12).filter((question) => question.dataset.result == 'correct').length;
    let wrong_number = questions.slice(0, 12).filter((question) => question.dataset.result == 'wrong').length;

    let url_params = new URLSearchParams(window.location.search);

    saveResults(correct_number, url_params.get('deck_id'));


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
 *  * @param {string} deckId - the edit deck form
 * 
 */
async function saveResults(score, deckId) {
    let result = await fetch('/api/save-results', {
        method: 'POST',
        body: JSON.stringify({
            score: score,
            deck_id: deckId
        })
    });

    if (!result.ok) {
        console.error(await result.text())
    } else {
        // REMOVE LATER
        console.log('error')
    }
}