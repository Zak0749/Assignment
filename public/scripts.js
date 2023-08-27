let searchBar = document.getElementById("searchbar");

let results = document.getElementById("results");
if (searchBar && results) {
  searchBar.addEventListener("input", async (event) => {
    if (!event.target.value) {
      results.innerHTML = "";
    } else {
      let response = await fetch(
        `/api/search_results?search_string=${event.target.value}`
      );
      results.innerHTML = await response.text();
    }
  });
}

let signin_form = document.getElementById("signin-form");

if (signin_form) {
  signin_form.addEventListener("submit", async (event) => {
    event.preventDefault();
    let data = {
      username: event.target.username.value,
      password: event.target.password.value,
    };
    let response = await fetch("api/signin", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    });

    if (response.status == 200) {
      window.location.replace("/account");
    } else {
      document.getElementById("form-error").innerText = await response.text();
    }
  });
}

let sign_out_button = document.getElementById("signout");
if (sign_out_button) {
  sign_out_button.addEventListener("click", async (event) => {
    event.preventDefault();
    let response = await fetch("api/signout");

    if (response.status == 200) {
      window.location.replace("/");
    }
  });
}
