<?php include "../src/components/navbar.php"; ?>

<div class="page">
    <header>
        <h1>Sign In</h1>
    </header>

    <main class="centre-main">
        <form id="signin-form">
            <div>
                <label for="username">Username</label>
                <input class="form-field" name="username" type="username" required />

                <label for="password">Password</label>
                <input class="form-field" name="password" type="password" required />

                <p style="text-align: center; margin-bottom: 10px">Already have an account?</p>

                <a href="/signup" class="form-button"><span class="material-symbols-outlined">
                        login
                    </span>Sign In</a>
            </div>

            <p class="error" id="form-error"></p>


            <button type="submit" value="Submit" class="form-button" type="submit"><span class="material-symbols-outlined">
                    check
                </span>Submit</button>
        </form>


    </main>
</div>