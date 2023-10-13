<fieldset name="questions" class="question form-field" oninput="matchHeights(this)">
                        <textarea placeholder="Key" name="key" class="question-key" required></textarea>
                        <textarea placeholder="Value" name="value" class="question-value" required></textarea>
                        <button class="question-delete-button" type="button" onclick="removeQuestion(this)">
                            <span class="material-symbols-outlined">
                                delete
                            </span>
                        </button>
                    </fieldset>

                    questions: Array.from(form.questions).map((question) => {
            return {
                key: question.getElementsByClassName("question-key")[0].value,
                value: question.getElementsByClassName("question-value")[0].value,
            }
        })

                    problem when getting as when getting if there was only one question iterating over would not work

                    had to use form.getElementsByClassname("question") instead of form.questions


                    had problem with error cos element is not focusable - added auto tab switcing on invalid to on oter page



                    was not sure how to get deleted questions lead me to rather than atcually delete add dataset val to remove this allowed me to keep track of deletions while aslo allowig and undo deletion feaure