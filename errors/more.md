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


-- next

i was using mysqlite but stuggled for more complex queries so switched to mysql and better lol anyway

when trying to do so couldn tnonnect mysql when using docker turns out sinse in container no use 127. ... i use the name in docker compose and it works


--- MORE 

-- Function for calculating the streak given by account_id 
-- Is complex and not possible to do in regular sql so extracting into function makes sense
CREATE FUNCTION user_streak (account_id UUID) RETURNS INT AS $$
#variable_conflict use_variable
-- Declare variables
DECLARE streak INT DEFAULT 0;
DECLARE date_diff INT;
BEGIN 
  -- Repeat for each item in column results
  FOR date_diff IN 
      SELECT EXTRACT(
        DAY FROM 
          LAG(timestamp, 1, CURRENT_TIMESTAMP) OVER (
            ORDER BY timestamp DESC
          ) 
         - timestamp
    ) as date_diff -- Gets the day part of the difference between the current row's timestamp and previous rows or for the first row the current timestamp
	  FROM play 
    WHERE play.account_id = user_id
  LOOP
  	IF date_diff > 1 THEN
      exit;
    END IF;
    streak := streak + 1;
  END LOOP;
  RETURN streak;
END $$ LANGUAGE plpgsql;

when muliple in one day counted so had to add else if = 1

ran into issue where I couln't have muliple count's in one SQL statement soluution: left join to sub query