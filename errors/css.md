-- the JS was trying to ajust the hiehgts so they share max and will auto elarge 

JS:
function auto_height(element) {
  console.log(element);
  let items = element.parentElement.children;

  items[0].style.height = "auto";
  items[1].style.height = "auto";

  if (items[0].scrollHeight > items[1].scrollHeight) {
    items[0].style.height = items[0].scrollHeight + "px";
    items[1].style.height = items[0].scrollHeight + "px";
  } else {
    items[0].style.height = items[1].scrollHeight + "px";
    items[1].style.height = items[1].scrollHeight + "px";
  }

  element.style.height = element.scrollHeight + "px";
}

-- but height got desycted sad

-- fixed by declaring varible before as when set on first line then read diff for second



-- NEXT

had errors adding question as when did the other reset had to change from innerHtml += 
to creating properly and appendChild


-- NEXT

-- User could select muliple answers
-- put fix here in js


-- NEXT
-- number of questions less than 4 caused errors make min questions four AHHHHH


== NEXT

gooogle charts problem color bad as hsl switched to chart.js

== NEXT

chart was displaying incorrectly if chaning type need wrapper:

new Chart(ctx, {
            type: "pie",
            data: {
                labels: [
                   ...
                ],
                datasets: [{
                    label: 'My First Dataset',
                    data: [correct, wrong],
                    backgroundColor: [
                        ...
                    ],
                    hoverOffset: 4
                }]
            }
        });

        before

        new Chart(ctx, {
            type: "pie",
                labels: [
                   ...
                ],
                datasets: [{
                    label: 'My First Dataset',
                    data: [correct, wrong],
                    backgroundColor: [
                        ...
                    ],
                    hoverOffset: 4
                }]
        });


        === doesn't support CSS varibles
 getComputedStyle(document.body).getPropertyValue("--accent")

=== Element still displayed even with height 0; 

problem padding 

added padding when displayed

=== NEXT

.deck-card {
    /* General size and colours */
    width: 150px;
    height: 150px;
    padding: 5px;
    border-radius: 10px;
    background-color: var(--secondary-background);
}

caused when clicked on edge not to take to link

moved to .deck-card 

== for form feilds was gonna use .user-invalid meaning if user had not interacted wouln't show up but not supported on all browsers so just invalid meaning it always there


== 

would not let submit agian after one wrong

async function submit_login(form) {
    let result = await fetch("/api/login", {
        method: "POST",
        body: JSON.stringify({
            username: form["username"].value,
            password: form["password"].value
        })
    });

    if (result.ok) {
        ...
    } else {
        let body = await result.json()

        let input = form[body["input-name"]]

        input.setCustomValidity(body.message)

        input.reportValidity()
    }
}

had to add oninput=this.setCustomValidity('') 

this resets when it changes so after an invalid submit it will allow another submition