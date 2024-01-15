## Ideas

- Flash Card app with KV pairs of definitions 
    - Allows for a range of different questions based on this
    - Streaks to encourage dailty practice

- Past Paper site hosting questions by course and KA
    - Allows users to record if they get the question right or wrong and point out weak areas
    dice bears


## Tech Specs

- google icons
- google fonts
- light / dark theme
- docker to deploy
- figma to create wire frames
- modern fetch js to allow for data without realoding


## reasearch points

-- How to get bool value for if other exists (saved)
-- % for sqlite search
-- Indexes to speed up searchs
-- How to send notifications
--  JS fetch API
-- Async functions
-- JS Fetch used cos better
-- extended class so one place the db thing is controled not everywhere which would be hard to change
-- used AI to implement test data 
-- HAd to us
-- CSS is chalanging as browsers implements CSS differently - colours highluight worked safair but not on chrome
-- Had to get bool for sql 
-- Used URL parameters for ids of decks and users therefor when shared everyone gets same result
-- struggled to work out how to count and average in query
-- COALESCE to find if zero
-- used normalise.css to get rid of anoying browser defaults creating incosistance - used online package as it doens;t need to be tested
-- dialog box to open up modal for tag select
-- dialog click outside handler as users expect on mobile

-- used robot icons as would be complex to create a editor and peeps would like thing to be like them so random no work but users are human so cool
-- spin css animation needed to use focus and atvie - active had to hold but found sollution on stack voerflow
-- extract URL param from css

-- data-seed property to store avatar on signup

-- look up for auto expanding text area: https://saturncloud.io/blog/creating-a-textarea-with-autoresize/ -23 to account for padding as line up

-- overflow-clip margin for horizontal list

-- decided to go with a patern where everything is extracted to other file allowing Model code to look good and logic to be seperate

-- things got very complicated so i decided to seperate into code file and view file to hold functions for most things

password_hash() and password verify for sequirty


use prepare n stuff to prevent hacking

-- chose to vaildate on both ends to ensure security

-- validate in php in seperate file as sqlite doesn;t support regexp
wrote own database abstarction to remove unesary code from the pages making the site cleaner

-- use regxp to check no special characters in JS
-- use regex to validate in php server side only use it as no messages need to be returned whereas JS more specific as help user
-- chartJS graphs <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

-- idea flash bacgrkound to hieghlight correct

SEQUIRITY 

-- htmlspecialchars() - stop html injections
-- prepare() - stop sql injections

-- try and fix padding, margin for elements as is always inconsistant 


## error

-- Password check will break

## mics
-- plays is seperate for anyomous users

## tongight

--work out how to auto have the 
-- add sequrity stuff in it to make sure others can URL manipulate

-- fix add auto height for edit onload


## TODO

-- EDIT DECK
-- DELETE DECK
-- PLAY
-- NOTIFICATIONS
-- VALIDATE ALL INPUTS ON SERVER

## if graph is broken need to add chart file before the mainnhead

# cases

files, urls: kebab case e.g. search-results
everything in js cammelCase e.g. searchResults
url params: snake_case
php var & func snake
php method cammel
json responces cammel

# get rid of the .php its dum

rather than use weird post can use url parms

https://rapidapi.com/guides/query-parameters-fetch

erorr messages on server do not need to give message as client knows about them so ovs some sus stuff going on

struggled to get streak

SELECT 
  timestamp,
  julianday(date(LAG ( timestamp, -1,  julianday("now") + 1 ) OVER ( 
		ORDER BY timestamp ASC
	))) - julianday(date(timestamp)) as untilNext
FROM Events
GROUP BY date(timestamp) 
ORDER BY date(timestamp) DESC

this gets the gap between dates

need to find a way to get the number of ones to start

PHP Cron SchedulerPHP Cron SchedulerPHP Cron SchedulerPHP Cron SchedulerPHP Cron SchedulerPHP Cron SchedulerPHP Cron SchedulerPHP Cron SchedulerPHP Cron SchedulerPHP Cron Scheduler
PHP Cron Scheduler
PHP Cron Scheduler

PHP Cron SchedulerPHP Cron Scheduler
PHP Cron Scheduler
PHP Cron Scheduler
PHP Cron Scheduler
PHP Cron Scheduler
PHP Cron Scheduler
PHP Cron Scheduler
PHP Cron Scheduler
v
PHP Cron Scheduler
PHP Cron Scheduler
PHP Cron Scheduler


PHP Cron SchedulerL

LOOK UP FURTURE ME OR ELSE!!!!!!!!!!!!!!!!


Try to avoid redirects to make nicer

Was planning to use a JS library for keyboard shortcuts but decided to write my own code as none of them did it inline so i used mousetrap with my own code to get it good

Tried to use mouse trap but deicde to use hotkeysw-js


##

key, value needs to be changed to question, answer

change question length to 64

https://learnsql.com/blog/how-to-calculate-length-of-series-in-sql/


WITH Dates AS (
  SELECT Date(timestamp) as streak_date FROM User_Play
)
SELECT
streak_date,
  DATEDIFF(lag_date, streak_date)
FROM (
  SELECT
    streak_date,
    IF(LAG(streak_date, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY)) OVER (ORDER BY streak_date DESC) as lag_date
 FROM Dates
) subquery

on this thought just could you datediff greatert than one but it no work so have to use vairble

use mysql functions to take stuff repeareed / complex stuff away 
