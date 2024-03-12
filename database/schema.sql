-- Table to store account information
CREATE TABLE public.account(
  -- Primary key ID field randomly generated on creation
  account_id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  -- A unique and required 16 character string
  -- Name for the account user
  username VARCHAR(16) UNIQUE NOT NULL,
  -- A required 255 long string to meet needs of php password_hash 
  -- A hashed user password
  password VARCHAR(255) NOT NULL,
  -- A required randomly generated hexadecimal of 8 length
  -- The seed for the users avatar
  avatar VARCHAR(8) NOT NULL,
  -- Date and time set when account created
  -- When the user created their account
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Stores a category
CREATE TABLE public.tag(
  -- Primary key ID field randomly generated on creation
  tag_id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  -- A required 24 length string
  -- The name of a tag
  title VARCHAR(24) NOT NULL
);
-- A tag that the user is interested in
CREATE TABLE public.follow(
  -- Required foreign key to the account_id on the account table
  -- Will be deleted when parent account is deleted
  -- The user that follows a topic
  account_id UUID NOT NULL REFERENCES public.account ON DELETE CASCADE,
  -- Required foreign key to the tag_id on the tag table
  -- Will be deleted when parent tag is deleted
  -- The tag that the user specified is follow
  tag_id UUID NOT NULL REFERENCES public.tag ON DELETE CASCADE,
  -- Composite primary key made of the account_id and tag_id
  -- As should only appear once can use composite key
  PRIMARY KEY (account_id, tag_id)
);
-- Table for a set of cards
CREATE TABLE public.deck(
  -- Primary key ID field randomly generated on creation
  deck_id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  -- Required foreign key to the account_id on the account table
  -- Will be deleted when parent account is deleted
  -- The account that created the deck
  account_id UUID NOT NULL REFERENCES public.account ON DELETE CASCADE,
  -- A required 32 long string
  -- The name of the deck
  title VARCHAR(32) NOT NULL,
  -- A required 256 long string
  -- An explanation given by the creator about the account
  description VARCHAR(256) NOT NULL,
  -- Date and time set when deck created
  -- When the user created their the deck
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Table to store cards each with a question and answer
CREATE TABLE public.card(
  -- Primary key ID field randomly generated on creation
  card_id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  -- Required foreign key to the deck_id on the deck table
  -- Will be deleted when parent deck is deleted
  -- The deck that the card belongs to
  deck_id UUID NOT NULL REFERENCES public.deck ON DELETE CASCADE,
  -- Required 256 length string
  question VARCHAR(256) NOT NULL,
  -- Required 256 length string
  answer VARCHAR(256) NOT NULL
);
-- A tag that a deck is related to
CREATE TABLE public.topic(
  -- Required foreign key to the deck_id on the deck table
  -- Will be deleted when parent deck is deleted
  -- The deck that this topic link is to
  deck_id UUID NOT NULL REFERENCES public.deck ON DELETE CASCADE,
  -- Required foreign key to the tag_id on the tag table
  -- Will be deleted when parent tag is deleted
  -- The tag associated with the above deck
  tag_id UUID NOT NULL REFERENCES public.tag ON DELETE CASCADE,
  -- Composite primary key made of the deck_id and tag_id
  -- As should only appear once can use composite key
  PRIMARY KEY (deck_id, tag_id)
);
-- A deck the user want's to remember for later
CREATE TABLE public.save(
  -- Required foreign key to the account_id on the account table
  -- Will be deleted when parent account is deleted
  -- The user that saved a specific deck
  account_id UUID NOT NULL REFERENCES public.account ON DELETE CASCADE,
  -- Required foreign key to the deck_id on the tag table
  -- Will be deleted when parent deck is deleted
  -- The deck that the used saved
  deck_id UUID NOT NULL REFERENCES public.deck ON DELETE CASCADE,
  -- Composite primary key made of the account_id and deck_id
  -- As should only appear once can use composite key
  PRIMARY KEY (account_id, deck_id)
);
-- Stores a record of a user playing a deck
CREATE TABLE public.play(
  -- Primary key ID field randomly generated on creation
  -- cannot use composite key of account and deck as user can play multiple times
  play_id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  -- Foreign key to the account_id on the account table
  -- Will be not when parent account is deleted
  -- The user that played a specific deck
  account_id UUID REFERENCES public.account ON DELETE
  SET NULL,
    -- Required foreign key to the deck_id on the tag table
    -- Will be deleted when parent deck is deleted
    -- The deck that the used played
    deck_id UUID NOT NULL REFERENCES public.deck ON DELETE CASCADE,
    -- Integer that stores the users score will be out of 12
    score INTEGER NOT NULL,
    -- Date and time set when deck created
    -- When the user played the deck
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Create index on searchable fields to improve performance
CREATE INDEX username_index ON public.account (username);
CREATE INDEX deck_title_index ON public.deck (title);
CREATE INDEX tag_index ON public.tag (title);
-- Function for finding out if the account by account_id has saved the deck specified by deck_id 
-- Takes in the UUID's of the deck and account and gives a boolean out
-- This is used repeated times in the code therefore makes sense to define it
CREATE FUNCTION is_saved(deck_id UUID, account_id UUID) RETURNS BOOLEAN AS $$ #variable_conflict use_variable
BEGIN -- If the account ID is set and a row exists for the deck_id and account_id
RETURN account_id IS NOT NULL
AND EXISTS(
  SELECT
  FROM save
  WHERE save.deck_id = deck_id
    AND save.account_id = account_id
);
END $$ LANGUAGE plpgsql;
CREATE FUNCTION is_followed(tag_id UUID, account_id UUID) RETURNS BOOLEAN AS $$ #variable_conflict use_variable
BEGIN -- If the account ID is set and a row exists for the deck_id and account_id
RETURN account_id IS NOT NULL
AND EXISTS(
  SELECT
  FROM follow
  WHERE follow.tag_id = tag_id
    AND follow.account_id = account_id
);
END $$ LANGUAGE plpgsql;
CREATE FUNCTION is_topic(tag_id UUID, deck_id UUID) RETURNS BOOLEAN AS $$ #variable_conflict use_variable
BEGIN -- If the account ID is set and a row exists for the deck_id and account_id
RETURN EXISTS(
  SELECT
  FROM topic
  WHERE topic.tag_id = tag_id
    AND topic.deck_id = deck_id
);
END $$ LANGUAGE plpgsql;
-- Function for calculating the streak given by account_id 
-- Is complex and not possible to do in regular sql so extracting into function makes sense
CREATE FUNCTION user_streak (account_id UUID) RETURNS INT AS $$ #variable_conflict use_variable
-- Declare variables
DECLARE streak INT DEFAULT 0;
DECLARE date_diff INT;
BEGIN -- Repeat for each item in column results
FOR date_diff IN
SELECT EXTRACT(
    DAY
    FROM LAG(timestamp, 1, CURRENT_TIMESTAMP) OVER (
        ORDER BY timestamp DESC
      )
  ) - EXTRACT (
    DAY
    FROM timestamp
  )
FROM play -- Gets the day part of the difference between the current row's timestamp and previous rows or for the first row the current timestamp
WHERE play.account_id = account_id LOOP -- If the user has broken their streak
  IF date_diff > 1 THEN exit;
-- Only want day differences of one as if it zero then same day
ELSIF date_diff = 1 THEN streak := streak + 1;
END IF;
END LOOP;
RETURN streak;
END $$ LANGUAGE plpgsql;
INSERT INTO account (
    account_id,
    username,
    password,
    avatar,
    timestamp
  )
VALUES (
    'a3511a35-6f9e-4147-bc11-3bd3b581efa1',
    'John_Doe',
    '$2y$10$ftxZhQIQZowmfXd6LKiQD.8NbvVcKLL8yowMKdORkmUVohG55HRlS',
    '1a3f9b7c',
    '2023-10-22 15:30:00'
  ),
  (
    '67e70953-55a0-4beb-9a97-6ff17af07691',
    'Emily_Smith',
    '$2y$10$SJK6Cv3hDHDmE9XMSV5hYudWdnUWzFxJIe6hH55616qV38Be1BOzC',
    'e8d24f6a',
    '2023-11-11 12:30:00'
  ),
  (
    '96dcf4e0-4095-43df-955b-8bfa54baaf97',
    'Sarah_Carter',
    '$2y$10$qgS6BKQkpjAlv.NMsiSqR.ztzK2BSBoAsVpbhiMz/BCriTOPh8iCe',
    'b0f63e2d',
    '2023-10-14 12:00:00'
  );
INSERT INTO deck (
    deck_id,
    account_id,
    title,
    description,
    timestamp
  )
VALUES (
    '20630b8d-7d55-486f-8070-1c0f4ff2fca6',
    '67e70953-55a0-4beb-9a97-6ff17af07691',
    'Security Risks and Precautions',
    'Remind yourself of the different simple attacks against your program and methods to prevent them.',
    '2023-11-30 12:00:00'
  ),
  (
    'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3',
    '96dcf4e0-4095-43df-955b-8bfa54baaf97',
    'Binary Basics',
    'The number system used by all computers. Commit the basics and representation of datatypes to your long term memory. ',
    '2023-11-02 12:30:00'
  ),
  (
    'ff0678dd-2331-4615-b6e8-230614961dd2',
    '67e70953-55a0-4beb-9a97-6ff17af07691',
    'Newtons Laws',
    'The fundamental laws of motion in physics. Practice answering the laws and what they are used for.',
    '2023-11-03 12:30:00'
  );
INSERT INTO tag(tag_id, title)
VALUES ('71def6cd-572f-4ecd-8a30-d51edba89d2a', 'Maths'),
  (
    '250fbc41-10c0-4b10-88ea-bf91c52f9918',
    'Physics'
  ),
  (
    '4e06f05f-16e1-4930-adca-355454466cee',
    'Computing Science'
  ),
  (
    'b5380134-4263-4c19-a0d1-37a0c5f3912e',
    'Chemistry'
  );
INSERT INTO topic (tag_id, deck_id)
VALUES (
    '250fbc41-10c0-4b10-88ea-bf91c52f9918',
    'ff0678dd-2331-4615-b6e8-230614961dd2'
  ),
  (
    '4e06f05f-16e1-4930-adca-355454466cee',
    'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3'
  ),
  (
    '71def6cd-572f-4ecd-8a30-d51edba89d2a',
    'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3'
  );
INSERT INTO follow (tag_id, account_id)
VALUES (
    '71def6cd-572f-4ecd-8a30-d51edba89d2a',
    'a3511a35-6f9e-4147-bc11-3bd3b581efa1'
  ),
  (
    '4e06f05f-16e1-4930-adca-355454466cee',
    'a3511a35-6f9e-4147-bc11-3bd3b581efa1'
  ),
  (
    '4e06f05f-16e1-4930-adca-355454466cee',
    '67e70953-55a0-4beb-9a97-6ff17af07691'
  );
INSERT INTO save (account_id, deck_id)
VALUES (
    'a3511a35-6f9e-4147-bc11-3bd3b581efa1',
    'ff0678dd-2331-4615-b6e8-230614961dd2'
  ),
  (
    'a3511a35-6f9e-4147-bc11-3bd3b581efa1',
    'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3'
  ),
  (
    '96dcf4e0-4095-43df-955b-8bfa54baaf97',
    'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3'
  );
INSERT INTO play (account_id, deck_id, score, timestamp)
VALUES (
    '67e70953-55a0-4beb-9a97-6ff17af07691',
    'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3',
    7,
    '2023-11-02 12:30:00'
  ),
  (
    '67e70953-55a0-4beb-9a97-6ff17af07691',
    'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3',
    8,
    '2023-11-19 12:30:00'
  ),
  (
    '67e70953-55a0-4beb-9a97-6ff17af07691',
    'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3',
    11,
    '2023-11-16 12:30:00'
  ),
  (
    '67e70953-55a0-4beb-9a97-6ff17af07691',
    'ff0678dd-2331-4615-b6e8-230614961dd2',
    4,
    '2023-11-23 02:30:00'
  ),
  (
    '96dcf4e0-4095-43df-955b-8bfa54baaf97',
    'ff0678dd-2331-4615-b6e8-230614961dd2',
    4,
    '2023-11-30 02:30:00'
  ),
  (
    '96dcf4e0-4095-43df-955b-8bfa54baaf97',
    'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3',
    10,
    '2023-11-12 12:30:00'
  ),
  (
    '96dcf4e0-4095-43df-955b-8bfa54baaf97',
    'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3',
    12,
    '2023-11-13 12:30:00'
  ),
  (
    '96dcf4e0-4095-43df-955b-8bfa54baaf97',
    'ff0678dd-2331-4615-b6e8-230614961dd2',
    7,
    '2023-11-7 02:30:00'
  );
INSERT INTO card (card_id, deck_id, question, answer)
VALUES (
    '4191bb49-94c9-45ad-828d-49d01780f6e4',
    '20630b8d-7d55-486f-8070-1c0f4ff2fca6',
    'Firewall',
    'Control network use'
  ),
  (
    'd3214777-f504-48b1-96a2-21ee0917dde2',
    '20630b8d-7d55-486f-8070-1c0f4ff2fca6',
    'XSS Attack',
    'Inject script tags into other users machines getting to run code on their browser'
  ),
  (
    'efda5e2c-2211-4a18-b387-30f3195bb17c',
    '20630b8d-7d55-486f-8070-1c0f4ff2fca6',
    'SQL Injection',
    'Injecting sql into database as string input is not sanitised '
  ),
  (
    '2cde1440-38e0-4a6e-9de1-15326308f7b3',
    '20630b8d-7d55-486f-8070-1c0f4ff2fca6',
    'DOS attack',
    'Spam requests to overload system'
  ),
  (
    'c002a33c-f0f4-4f8b-81d6-685600f986e2',
    '20630b8d-7d55-486f-8070-1c0f4ff2fca6',
    'Prevent XSS Attack',
    'Use htmlspecialchars when outputting'
  ),
  (
    '9d36af5f-1883-4a30-a60e-6cebd5fcea55',
    '20630b8d-7d55-486f-8070-1c0f4ff2fca6',
    'Prevent SQL Injection',
    'Use prepared statements'
  ),
  (
    'cab96744-15d0-4435-bdb6-5a392c7b8d78',
    '20630b8d-7d55-486f-8070-1c0f4ff2fca6',
    'Prevent DOS attack',
    'Timeout on lots of requests'
  ),
  (
    'ad11d162-24a5-40f4-ab05-fb004a532be3',
    '20630b8d-7d55-486f-8070-1c0f4ff2fca6',
    'Encryption ',
    'Scrambles the data so even if attacker gets it, they cannot read'
  ),
  (
    'f725745c-81af-4b08-8e3f-803aacae780c',
    '20630b8d-7d55-486f-8070-1c0f4ff2fca6',
    'Private Key',
    'A key that is used to decrypt data'
  ),
  (
    'a0670d92-f911-4f01-b8d4-bad8ffe68d8b',
    '20630b8d-7d55-486f-8070-1c0f4ff2fca6',
    'Public key',
    'A key used to encrypt data'
  ),
  (
    '2737831d-cd3f-4a76-9974-f0a2f603ef14',
    '20630b8d-7d55-486f-8070-1c0f4ff2fca6',
    'Digital Certificate',
    'Verifies a user sending a message is who they claim'
  ),
  (
    '8396bf26-c8b4-4eb0-9386-05189fafba3e',
    '20630b8d-7d55-486f-8070-1c0f4ff2fca6',
    'Digital Signatures',
    'Verifies an electronic message or document is authentic'
  ),
  (
    'c3cead37-e1a6-427b-906b-1372fdb8168b',
    '20630b8d-7d55-486f-8070-1c0f4ff2fca6',
    'Tracking Cookies',
    'Used to track users across websites'
  ),
  (
    '0ab258f6-6721-4e34-bed3-1940745aba67',
    'ff0678dd-2331-4615-b6e8-230614961dd2',
    'Newtons 1st law',
    'An object at rest remains at rest, and an object in motion remains in motion at constant speed and in a straight line unless acted on by an unbalanced force.'
  ),
  (
    '8da557c8-b6df-4961-b7ab-b697545a710d',
    'ff0678dd-2331-4615-b6e8-230614961dd2',
    'Newtons 2nd law',
    'F = ma'
  ),
  (
    '337a5f16-86ba-490e-95bb-c4ae5611d943',
    'ff0678dd-2331-4615-b6e8-230614961dd2',
    'Newtons 3rd law',
    'Whenever one object exerts a force on another object, the second object exerts an equal and opposite on the first.'
  ),
  (
    '2896c6d3-40d9-4617-a750-69ef21d60856',
    'ff0678dd-2331-4615-b6e8-230614961dd2',
    'Unit of force',
    'Newtons'
  ),
  (
    'bc13f4e2-63c2-45d8-84af-8f061cd92752',
    'ff0678dd-2331-4615-b6e8-230614961dd2',
    'Unit of acceleration',
    'Meters per second per second'
  ),
  (
    'c136cde9-2ad3-4af5-8ad3-35e21d5d12ad',
    'ff0678dd-2331-4615-b6e8-230614961dd2',
    'Unit of mass',
    'Kilograms'
  ),
  (
    '8d343435-0c7a-4617-b9ce-bf74929ca196',
    'ff0678dd-2331-4615-b6e8-230614961dd2',
    'Rocket is propelled by',
    'Newtons 3rd law'
  ),
  (
    'c4b56f27-adb2-440a-b2f2-8c77abcc97c2',
    'ff0678dd-2331-4615-b6e8-230614961dd2',
    'A spaceship doesn’t slow down because',
    'Newtons 1st laws'
  ),
  (
    'b474e7c0-48b0-47f8-b593-fb110352dc57',
    'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3',
    'What base is binary',
    'Base 2'
  ),
  (
    '5bb3e3ea-f2cf-4a3a-be1a-3d1966cc66ee',
    'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3',
    'What are the two allowed symbols',
    '1 and 0'
  ),
  (
    '841e5bb0-7362-4c0a-b338-3407829da997',
    'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3',
    'Why is binary useful',
    'Computers can only store 1’s or 0’s'
  ),
  (
    'ad4cf1bc-75ec-451d-a9bc-8c44f494e220',
    'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3',
    'Represent Letters',
    'ASCII'
  ),
  (
    '74cf35d5-2a8c-4b32-bd52-11d7ffefb86f',
    'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3',
    'Represent Images',
    'Array of pixels'
  ),
  (
    'e14ce74c-fc1f-40d0-bd60-187336e53df9',
    'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3',
    'Range of a 8 bit unsigned integer ',
    '0 -> 255'
  ),
  (
    '29ae95ae-fc6f-4b51-9b96-43a28cffbd7e',
    'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3',
    'Range of a 8 bit signed integer',
    '-128 -> 127'
  ),
  (
    '34421b81-18df-483e-93de-fd8f07a2ab80',
    'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3',
    'Represent Decimal Numbers',
    'Floating point system'
  );
-- -- Test Data
INSERT INTO public.account (
    account_id,
    username,
    password,
    avatar,
    timestamp
  )
VALUES (
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    'john_doe',
    '$2y$10$Ya/zWJOE4bsmplhqh9VKkefUwFLvQKn89uxoKjF1UJFPzvlzOYtpm',
    'a1b2c3d4',
    '2023-11-11T12:30:00'
  ),
  -- password123
  (
    '7c9e6679-8b75-4d4b-a000-9c29efaa44b3',
    'jane_smith',
    '$2y$10$wuUeddhloqkbjH8wNnrnJ.iCqyb4qFNlDnwv/gtDkm3ZfoPFwePq2',
    'e5f6g7h8',
    '2023-11-11T12:35:00'
  ),
  --securepass456
  (
    'f47ac10b-58cc-4372-a567-0e02b2c3d479',
    'bob_jones',
    '$2y$10$xOVZRSB212nfqQRD.ExYMOFCDhHbfP2DMfayvVSrIVtZev2qrCVeO',
    'i9j0k1l2',
    '2023-11-11T12:40:00'
  ),
  -- 'myp@ssword
  (
    'd82f9c72-ff43-429a-886f-cc961b308e91',
    'alice_green',
    '$2y$10$OSePb/DS4dOi6JpncitN8.3JqEo0OVzl8klROPVk143tragHpv0be',
    'm3n4o5p6',
    '2023-11-11T12:45:00'
  ),
  --strongPassword
  (
    '9c8e8f52-8a40-43d3-a050-482c3282adbc',
    'sam_wilson',
    '$2y$10$5L0TnrxSFj248xcp9EyxzeA2RafYHBdGHW.8hut7qAJqASvdgtXBm',
    'q7r8s9t0',
    '2023-11-11T12:50:00'
  ),
  --pass1234
  (
    'edc1e69a-ff46-493f-bc44-3e875a21177d',
    'lisa_miller',
    '$2y$10$jXCXLRb.9JMV7j9O3osdfeqYfdetEwl.oc2efIcwJbklVq4P5idxO',
    'u1v2w3x4',
    '2023-11-11T12:55:00'
  ),
  --password!23
  (
    'eb7a1c0e-522c-4f40-83e9-3b3b7ce34c65',
    'michael_knight',
    '$2y$10$qTNP/BKEr9dt3JB/jGnQ9us7UDhDk7eRN7VggmxAVcb2Pkk02HlpS',
    'y5z6a7b8',
    '2023-11-11T13:00:00'
  ),
  --kn1ghtRider
  (
    '74316e29-57dd-43b6-8aae-92a03df6f8eb',
    'emily_white',
    '$2y$10$LMWotrg5k7oa8/TWKCh3XO3lloFbFVh5TZkGarcbhcTpsQfbjFHrC',
    'c9d0e1f2',
    '2023-11-11T13:05:00'
  ),
  --p@ssw0rd
  (
    '4654a13b-0909-45c6-bc5a-674a0793e63b',
    'ryan_black',
    '$2y$10$uc1YgA2UPmx5JFa3wHoiLOJa7Av2K8oxMkXYgvo4Z4vBDsyMWHeEy',
    'g3h4i5j6',
    '2023-11-11T13:10:00'
  ),
  --blackp@ss
  (
    'c5c83c5b-7e83-4935-8a67-58939225a1e9',
    'susan_blue',
    '$2y$10$gakDNXY4s4vAOecxAfZaHuH0kjLTNBvRaFn9GGHteX5mzKbbqgH3C',
    'k7l8m9n0',
    '2023-11-11T13:15:00'
  );
-- 
INSERT INTO public.deck (
    deck_id,
    account_id,
    title,
    description,
    timestamp
  )
VALUES (
    'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    'History Flashcards',
    'Flashcards about historical events',
    '2023-11-10T14:30:00'
  ),
  (
    'd570c9c5-1342-4c5d-b82a-2491f45008b3',
    'd82f9c72-ff43-429a-886f-cc961b308e91',
    'Mathematics Fundamentals',
    'Fundamental concepts in mathematics',
    '2023-11-10T14:35:00'
  ),
  (
    'ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9',
    '7c9e6679-8b75-4d4b-a000-9c29efaa44b3',
    'Literature Masterpieces',
    'Flashcards about classic literature',
    '2023-11-10T14:40:00'
  ),
  (
    'bdf8c6ef-38fb-4c35-b103-b96a47a0e4da',
    'eb7a1c0e-522c-4f40-83e9-3b3b7ce34c65',
    'Chemistry Basics',
    'Basic concepts in chemistry',
    '2023-11-10T14:45:00'
  ),
  (
    '9b628635-bf41-4cb5-86d1-9c8c754c46b1',
    '9c8e8f52-8a40-43d3-a050-482c3282adbc',
    'Spanish Vocabulary',
    'Flashcards to build Spanish vocabulary',
    '2023-11-10T14:50:00'
  ),
  (
    '4f5c9977-35ee-4dcd-953d-d2c4ba17be38',
    'edc1e69a-ff46-493f-bc44-3e875a21177d',
    'Programming Concepts',
    'Fundamental concepts in programming',
    '2023-11-10T14:55:00'
  ),
  (
    'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    'Health and Fitness Tips',
    'Tips for a healthy lifestyle',
    '2023-11-10T15:00:00'
  ),
  (
    '15e07d6f-207c-4747-b06b-3d9cb1de1d8d',
    '9c8e8f52-8a40-43d3-a050-482c3282adbc',
    'Art History Highlights',
    'Flashcards about famous artworks and artists',
    '2023-11-10T15:05:00'
  ),
  (
    '228d305f-d6e9-473d-b502-79f0ebed7188',
    '9c8e8f52-8a40-43d3-a050-482c3282adbc',
    'Crime and law',
    'Flashcards about famous artworks and artists',
    '2023-11-10T15:05:00'
  ),
  (
    'da91fc42-091c-4f80-896f-0a4827d760d0',
    '9c8e8f52-8a40-43d3-a050-482c3282adbc',
    'MacCaig poetry',
    'Flashcards about famous artworks and artists',
    '2023-11-10T15:05:00'
  ),
  (
    '74cc1600-a3f7-4423-93ff-0967a2b9d71c',
    '9c8e8f52-8a40-43d3-a050-482c3282adbc',
    'Quantum Physics',
    'Flashcards about famous artworks and artists',
    '2023-11-10T15:05:00'
  ),
  (
    '572766eb-a8b0-421d-880f-6fea8efb6f74',
    '9c8e8f52-8a40-43d3-a050-482c3282adbc',
    'Engineering schematics',
    'Flashcards about famous artworks and artists',
    '2023-11-10T15:05:00'
  ),
  (
    'd5220719-8d62-4705-bf6f-0291eaf88fc5',
    '9c8e8f52-8a40-43d3-a050-482c3282adbc',
    'Maths Identities',
    'Flashcards about famous artworks and artists',
    '2023-11-11T15:05:00'
  ),
  (
    'df5e19a0-c98d-410f-976e-f18e3bf9bbe4',
    '9c8e8f52-8a40-43d3-a050-482c3282adbc',
    'French Openings',
    'Flashcards about famous artworks and artists',
    '2023-11-10T15:05:00'
  );
INSERT INTO public.card (deck_id, question, answer)
VALUES (
    'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1',
    'What year did World War II end?',
    '1945'
  ),
  (
    'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1',
    'Who was the U.S. president during World War II?',
    'Franklin D. Roosevelt'
  ),
  (
    'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1',
    'country was the first to use atomic bombs in warfare?',
    'United States'
  ),
  (
    'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1',
    'the chemical symbol for uranium?',
    'U'
  ),
  (
    'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1',
    'In  year did the Cold War end?',
    '1991'
  ),
  (
    'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1',
    'the capital of Germany?',
    'Berlin'
  ),
  (
    'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1',
    'Who wrote "To Kill a Mockingbird"?',
    'Harper Lee'
  ),
  (
    'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1',
    'city hosted the 2016 Summer Olympics?',
    'Rio de Janeiro'
  ),
  (
    'd570c9c5-1342-4c5d-b82a-2491f45008b3',
    'the value of pi (π)?',
    '3.14159'
  ),
  (
    'd570c9c5-1342-4c5d-b82a-2491f45008b3',
    'known as the "Father of Mathematics"?',
    'Archimedes'
  ),
  (
    'd570c9c5-1342-4c5d-b82a-2491f45008b3',
    'the Pythagorean theorem?',
    'a² + b² = c²'
  ),
  (
    'd570c9c5-1342-4c5d-b82a-2491f45008b3',
    'the sum of the angles in a triangle?',
    '180 degrees'
  ),
  (
    'd570c9c5-1342-4c5d-b82a-2491f45008b3',
    'Who developed the laws of motion?',
    'Isaac Newton'
  ),
  (
    'd570c9c5-1342-4c5d-b82a-2491f45008b3',
    'the capital of France?',
    'Paris'
  ),
  (
    'd570c9c5-1342-4c5d-b82a-2491f45008b3',
    'Who wrote "The Hitchhikers Guide to the Galaxy"?',
    'Douglas Adams'
  ),
  (
    'ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9',
    'Who wrote "Romeo and Juliet"?',
    'William Shakespeare'
  ),
  (
    'ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9',
    'In  century did the Renaissance begin?',
    '14th century'
  ),
  (
    'ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9',
    'Who painted the Mona Lisa?',
    'Leonardo da Vinci'
  ),
  (
    'ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9',
    'In  city is the Louvre Museum located?',
    'Paris'
  ),
  (
    'ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9',
    'considered the father of modern physics?',
    'Albert Einstein'
  ),
  (
    'ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9',
    'the theory of relativity?',
    'E=mc²'
  ),
  (
    'ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9',
    'known as the "Bard of Avon"?',
    'William Shakespeare'
  ),
  (
    'ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9',
    'the meaning of the term "Renaissance"?',
    'Rebirth'
  ),
  (
    'bdf8c6ef-38fb-4c35-b103-b96a47a0e4da',
    'the chemical symbol for gold?',
    'Au'
  ),
  (
    'bdf8c6ef-38fb-4c35-b103-b96a47a0e4da',
    'a chemical element?',
    'A substance that cannot be broken down into simpler substances by chemical means'
  ),
  (
    'bdf8c6ef-38fb-4c35-b103-b96a47a0e4da',
    'the most abundant gas in Earths atmosphere?',
    'Nitrogen'
  ),
  (
    'bdf8c6ef-38fb-4c35-b103-b96a47a0e4da',
    'the chemical formula for water?',
    'H₂O'
  ),
  (
    'bdf8c6ef-38fb-4c35-b103-b96a47a0e4da',
    'a chemical reaction?',
    'The process by  substances are transformed into different substances'
  ),
  (
    'bdf8c6ef-38fb-4c35-b103-b96a47a0e4da',
    'the pH scale used for?',
    'Measuring the acidity or basicity of a solution'
  ),
  (
    'bdf8c6ef-38fb-4c35-b103-b96a47a0e4da',
    'known as the "Father of Chemistry"?',
    'Antoine Lavoisier'
  ),
  (
    'bdf8c6ef-38fb-4c35-b103-b96a47a0e4da',
    'an isotope?',
    'Atoms of the same element with different numbers of neutrons'
  ),
  (
    '9b628635-bf41-4cb5-86d1-9c8c754c46b1',
    'How do you say "hello" in Spanish?',
    'Hola'
  ),
  (
    '9b628635-bf41-4cb5-86d1-9c8c754c46b1',
    'the capital of Spain?',
    'Madrid'
  ),
  (
    '9b628635-bf41-4cb5-86d1-9c8c754c46b1',
    'a famous Spanish artist known for his surrealist paintings?',
    'Salvador Dalí'
  ),
  (
    '9b628635-bf41-4cb5-86d1-9c8c754c46b1',
    'the currency of Spain?',
    'Euro'
  ),
  (
    '9b628635-bf41-4cb5-86d1-9c8c754c46b1',
    'spanish dish consisting of rice, saffron, meats and vegetables?',
    'Paella'
  ),
  (
    '9b628635-bf41-4cb5-86d1-9c8c754c46b1',
    'Spanish architect known for his unique and avant-garde designs?',
    'Antoni Gaudí'
  ),
  (
    '9b628635-bf41-4cb5-86d1-9c8c754c46b1',
    'the largest city in Spain?',
    'Madrid'
  ),
  (
    '4f5c9977-35ee-4dcd-953d-d2c4ba17be38',
    'a loop in programming?',
    'A sequence of instructions that is continually repeated until a certain condition is met'
  ),
  (
    '4f5c9977-35ee-4dcd-953d-d2c4ba17be38',
    'the difference between "for" and "while" loops?',
    '"For" loops are used when the number of iterations is known, while "while" loops are used when the condition for termination is not known initially'
  ),
  (
    '4f5c9977-35ee-4dcd-953d-d2c4ba17be38',
    'the purpose of conditional statements in programming?',
    'To make decisions and execute different code blocks based on specific conditions'
  ),
  (
    '4f5c9977-35ee-4dcd-953d-d2c4ba17be38',
    'an array?',
    'A data structure that stores a collection of elements, each identified by an index or a key'
  ),
  (
    '4f5c9977-35ee-4dcd-953d-d2c4ba17be38',
    'the difference between a function and a method in programming?',
    'A function is a block of code that performs a specific task, while a method is a function associated with an object'
  ),
  (
    '4f5c9977-35ee-4dcd-953d-d2c4ba17be38',
    'the purpose of comments in code?',
    'To provide explanations or additional information for human readers and developers'
  ),
  (
    '4f5c9977-35ee-4dcd-953d-d2c4ba17be38',
    'the concept of "scope" in programming?',
    'The region of the program where a variable can be accessed'
  ),
  (
    '4f5c9977-35ee-4dcd-953d-d2c4ba17be38',
    'the role of a version control system in software development?',
    'To track changes in the source code and coordinate work among multiple developers'
  ),
  (
    'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b',
    'What are some tips for a healthy lifestyle?',
    'Exercise regularly, eat a balanced diet, get enough sleep'
  ),
  (
    'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b',
    'How many hours of sleep are recommended for adults?',
    '7-9 hours'
  ),
  (
    'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b',
    'the importance of staying hydrated?',
    'It helps maintain bodily functions, regulate temperature, and support overall health'
  ),
  (
    'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b',
    'the recommended daily intake of fruits and vegetables?',
    '5 servings'
  ),
  (
    'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b',
    'How does regular exercise benefit mental health?',
    'It reduces stress, improves mood, and enhances cognitive function'
  ),
  (
    'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b',
    'the significance of a balanced diet?',
    'It provides essential nutrients for the body to function properly'
  ),
  (
    'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b',
    'Why is it important to manage stress?',
    'High stress levels can negatively impact physical and mental health'
  ),
  (
    'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b',
    'What are some benefits of adequate sleep?',
    'Improved concentration, mood, and overall well-being'
  ),
  (
    '15e07d6f-207c-4747-b06b-3d9cb1de1d8d',
    'Who painted the Mona Lisa?',
    'Leonardo da Vinci'
  ),
  (
    '15e07d6f-207c-4747-b06b-3d9cb1de1d8d',
    'In  century did the Renaissance occur?',
    '14th to 17th century'
  ),
  (
    '15e07d6f-207c-4747-b06b-3d9cb1de1d8d',
    'the significance of the Sistine Chapel ceiling?',
    'Painted by Michelangelo, it is considered a masterpiece of Renaissance art'
  ),
  (
    '15e07d6f-207c-4747-b06b-3d9cb1de1d8d',
    'Who sculpted the statue of David?',
    'Michelangelo'
  ),
  (
    '15e07d6f-207c-4747-b06b-3d9cb1de1d8d',
    'artist known for realistic portrayals rural life?',
    'Pieter Bruegel the Elder'
  ),
  (
    '15e07d6f-207c-4747-b06b-3d9cb1de1d8d',
    'chiaroscuro in art?',
    'The use of strong contrasts between light and dark to create a sense of volume and three-dimensionality'
  ),
  (
    '15e07d6f-207c-4747-b06b-3d9cb1de1d8d',
    'considered the founder of Impressionism?',
    'Claude Monet'
  ),
  (
    '15e07d6f-207c-4747-b06b-3d9cb1de1d8d',
    'surrealism in art?',
    'A 20th-century avant-garde movement that sought to release the creative potential of the unconscious mind'
  );
INSERT INTO public.tag (tag_id, title)
VALUES (
    '3e25960a-58c6-4f01-81f3-2c7a6c0a1111',
    'History'
  ),
  (
    'b91cbbb1-cc6a-4e0b-a788-133e071f3333',
    'Literature'
  ),
  (
    'ce072a37-051f-4a6b-87b3-1ef236734444',
    'Science'
  ),
  (
    'e98643b1-83cf-4f21-9361-798c49c55555',
    'Language'
  ),
  (
    '6f6c6887-7070-45ef-95a2-9b53b2356666',
    'Programming'
  ),
  ('bd822ac2-1ae7-4daa-8f15-80989ff77777', 'Health'),
  ('67ea0bc3-13c9-4bb3-b6eb-8ef48a288888', 'Art'),
  (
    'b512d3e5-9e29-4b3d-98bb-d3aa10599999',
    'Technology'
  ),
  (
    '24a4f0b9-0d12-4e62-831b-5b6ce9671010',
    'Environment'
  ),
  ('727a0b1b-60ff-4b6d-8a15-ba7a43212121', 'Python'),
  (
    'bb7a6dd4-c2e2-4e79-8140-ebc0e4f31313',
    'Geography'
  ),
  (
    '5558d622-6940-4c39-8d8e-6972a7a11414',
    'Economics'
  ),
  ('ff5260d2-0eb5-4b8c-99d0-9d62efb51515', 'Music'),
  ('c77f5f63-47a7-46d5-88d1-419a98e51616', 'French');
INSERT INTO public.topic (deck_id, tag_id)
VALUES (
    'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1',
    '3e25960a-58c6-4f01-81f3-2c7a6c0a1111'
  ),
  (
    'd570c9c5-1342-4c5d-b82a-2491f45008b3',
    '71def6cd-572f-4ecd-8a30-d51edba89d2a'
  ),
  (
    'ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9',
    'b91cbbb1-cc6a-4e0b-a788-133e071f3333'
  ),
  (
    'bdf8c6ef-38fb-4c35-b103-b96a47a0e4da',
    'ce072a37-051f-4a6b-87b3-1ef236734444'
  ),
  (
    '9b628635-bf41-4cb5-86d1-9c8c754c46b1',
    'e98643b1-83cf-4f21-9361-798c49c55555'
  ),
  (
    '4f5c9977-35ee-4dcd-953d-d2c4ba17be38',
    '6f6c6887-7070-45ef-95a2-9b53b2356666'
  ),
  (
    'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b',
    'bd822ac2-1ae7-4daa-8f15-80989ff77777'
  ),
  (
    '15e07d6f-207c-4747-b06b-3d9cb1de1d8d',
    '67ea0bc3-13c9-4bb3-b6eb-8ef48a288888'
  ),
  (
    '228d305f-d6e9-473d-b502-79f0ebed7188',
    '3e25960a-58c6-4f01-81f3-2c7a6c0a1111'
  ),
  (
    'da91fc42-091c-4f80-896f-0a4827d760d0',
    'b91cbbb1-cc6a-4e0b-a788-133e071f3333'
  ),
  (
    '74cc1600-a3f7-4423-93ff-0967a2b9d71c',
    '250fbc41-10c0-4b10-88ea-bf91c52f9918'
  ),
  (
    '572766eb-a8b0-421d-880f-6fea8efb6f74',
    'ce072a37-051f-4a6b-87b3-1ef236734444'
  ),
  (
    'd5220719-8d62-4705-bf6f-0291eaf88fc5',
    '71def6cd-572f-4ecd-8a30-d51edba89d2a'
  ),
  (
    'df5e19a0-c98d-410f-976e-f18e3bf9bbe4',
    'c77f5f63-47a7-46d5-88d1-419a98e51616'
  );
INSERT INTO public.follow (account_id, tag_id)
VALUES (
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    '3e25960a-58c6-4f01-81f3-2c7a6c0a1111'
  ),
  (
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    'b91cbbb1-cc6a-4e0b-a788-133e071f3333'
  ),
  (
    'f47ac10b-58cc-4372-a567-0e02b2c3d479',
    '71def6cd-572f-4ecd-8a30-d51edba89d2a'
  ),
  (
    'f47ac10b-58cc-4372-a567-0e02b2c3d479',
    'e98643b1-83cf-4f21-9361-798c49c55555'
  ),
  (
    'f47ac10b-58cc-4372-a567-0e02b2c3d479',
    'ce072a37-051f-4a6b-87b3-1ef236734444'
  ),
  (
    'f47ac10b-58cc-4372-a567-0e02b2c3d479',
    '6f6c6887-7070-45ef-95a2-9b53b2356666'
  ),
  (
    'd82f9c72-ff43-429a-886f-cc961b308e91',
    'e98643b1-83cf-4f21-9361-798c49c55555'
  ),
  (
    'd82f9c72-ff43-429a-886f-cc961b308e91',
    'bd822ac2-1ae7-4daa-8f15-80989ff77777'
  ),
  (
    '9c8e8f52-8a40-43d3-a050-482c3282adbc',
    '67ea0bc3-13c9-4bb3-b6eb-8ef48a288888'
  ),
  (
    '9c8e8f52-8a40-43d3-a050-482c3282adbc',
    'b512d3e5-9e29-4b3d-98bb-d3aa10599999'
  ),
  (
    'edc1e69a-ff46-493f-bc44-3e875a21177d',
    '6f6c6887-7070-45ef-95a2-9b53b2356666'
  ),
  (
    'eb7a1c0e-522c-4f40-83e9-3b3b7ce34c65',
    'bd822ac2-1ae7-4daa-8f15-80989ff77777'
  ),
  (
    'eb7a1c0e-522c-4f40-83e9-3b3b7ce34c65',
    '67ea0bc3-13c9-4bb3-b6eb-8ef48a288888'
  ),
  (
    '74316e29-57dd-43b6-8aae-92a03df6f8eb',
    'e98643b1-83cf-4f21-9361-798c49c55555'
  ),
  (
    '4654a13b-0909-45c6-bc5a-674a0793e63b',
    'ce072a37-051f-4a6b-87b3-1ef236734444'
  ),
  (
    '4654a13b-0909-45c6-bc5a-674a0793e63b',
    '24a4f0b9-0d12-4e62-831b-5b6ce9671010'
  ),
  (
    'c5c83c5b-7e83-4935-8a67-58939225a1e9',
    'b512d3e5-9e29-4b3d-98bb-d3aa10599999'
  ),
  (
    'c5c83c5b-7e83-4935-8a67-58939225a1e9',
    'ce072a37-051f-4a6b-87b3-1ef236734444'
  );
INSERT INTO public.save (account_id, deck_id)
VALUES (
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1'
  ),
  (
    '7c9e6679-8b75-4d4b-a000-9c29efaa44b3',
    'd570c9c5-1342-4c5d-b82a-2491f45008b3'
  ),
  (
    'f47ac10b-58cc-4372-a567-0e02b2c3d479',
    'ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9'
  ),
  (
    'd82f9c72-ff43-429a-886f-cc961b308e91',
    'bdf8c6ef-38fb-4c35-b103-b96a47a0e4da'
  ),
  (
    '9c8e8f52-8a40-43d3-a050-482c3282adbc',
    '9b628635-bf41-4cb5-86d1-9c8c754c46b1'
  ),
  (
    'edc1e69a-ff46-493f-bc44-3e875a21177d',
    '4f5c9977-35ee-4dcd-953d-d2c4ba17be38'
  ),
  (
    'eb7a1c0e-522c-4f40-83e9-3b3b7ce34c65',
    'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b'
  ),
  (
    '74316e29-57dd-43b6-8aae-92a03df6f8eb',
    '15e07d6f-207c-4747-b06b-3d9cb1de1d8d'
  );
INSERT INTO public.play (play_id, account_id, deck_id, score)
VALUES (
    '4e4bfcc9-13cd-4e2a-b2c4-c0af4d2e1111',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1',
    80
  ),
  (
    '50c1c87d-b2e7-47c2-9ed2-d076d1c32222',
    '7c9e6679-8b75-4d4b-a000-9c29efaa44b3',
    'd570c9c5-1342-4c5d-b82a-2491f45008b3',
    95
  ),
  (
    '5c38fddb-bf39-43e7-af61-853924111333',
    'f47ac10b-58cc-4372-a567-0e02b2c3d479',
    'ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9',
    75
  ),
  (
    '62e6c333-2e25-4b7f-b26b-868898555555',
    'd82f9c72-ff43-429a-886f-cc961b308e91',
    'bdf8c6ef-38fb-4c35-b103-b96a47a0e4da',
    90
  ),
  (
    '6d839522-7c45-4fe7-a6e1-620ce56b6b66',
    '9c8e8f52-8a40-43d3-a050-482c3282adbc',
    '9b628635-bf41-4cb5-86d1-9c8c754c46b1',
    85
  ),
  (
    '7dd5816e-9e8a-4b0d-a7c4-8fb550aaf111',
    'edc1e69a-ff46-493f-bc44-3e875a21177d',
    '4f5c9977-35ee-4dcd-953d-d2c4ba17be38',
    70
  ),
  (
    '838aa4ae-736c-4918-8e9d-2e4c1c116688',
    'eb7a1c0e-522c-4f40-83e9-3b3b7ce34c65',
    'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b',
    88
  ),
  (
    '8cf41a0f-2334-4c06-926c-d2f8ea111999',
    '74316e29-57dd-43b6-8aae-92a03df6f8eb',
    '15e07d6f-207c-4747-b06b-3d9cb1de1d8d',
    92
  );
INSERT INTO public.play (account_id, deck_id, score, timestamp)
VALUES (
    'a3511a35-6f9e-4147-bc11-3bd3b581efa1',
    'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1',
    8,
    '2024-02-25T14:30:00'
  ),
  (
    'a3511a35-6f9e-4147-bc11-3bd3b581efa1',
    'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1',
    4,
    '2024-02-24T14:30:00'
  ),
  (
    'a3511a35-6f9e-4147-bc11-3bd3b581efa1',
    'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1',
    11,
    '2024-02-23T14:30:00'
  );

INSERT INTO tag (tag_id, title) VALUES
  ('294d1c54-9b5b-428f-9bcc-4e6433b691f5', 'test tag');

INSERT INTO tag (title)
VALUES 
('test tag 1'),
('test tag 2'),
('test tag 3'),
('test tag 4'),
('test tag 5'),
('test tag 6'),
('test tag 7'),
('test tag 8'),
('test tag 9'),
('test tag 10'),
('test tag 11'),
('test tag 12'),
('test tag 13'),
('test tag 14'),
('test tag 15'),
('test tag 16'),
('test tag 17'),
('test tag 18'),
('test tag 19'),
('test tag 20'),
('test tag 21'),
('test tag 22'),
('test tag 23'),
('test tag 24');


INSERT INTO account (username, password, avatar) VALUES

  ('test account 0', 'password', '123456'),('test account 1', 'password', '123456'),('test account 2', 'password', '123456'),('test account 3', 'password', '123456'),('test account 4', 'password', '123456'),('test account 5', 'password', '123456'),('test account 6', 'password', '123456'),('test account 7', 'password', '123456'),('test account 8', 'password', '123456'),('test account 9', 'password', '123456'),('test account 10', 'password', '123456'),('test account 11', 'password', '123456'),('test account 12', 'password', '123456'),('test account 13', 'password', '123456'),('test account 14', 'password', '123456'),('test account 15', 'password', '123456'),('test account 16', 'password', '123456'),('test account 17', 'password', '123456'),('test account 18', 'password', '123456'),('test account 19', 'password', '123456'),('test account 20', 'password', '123456'),('test account 21', 'password', '123456'),('test account 22', 'password', '123456'),('test account 23', 'password', '123456'),('test account 24', 'password', '123456');

INSERT INTO deck (deck_id, title, account_id, description)
VALUES (
  '0037541d-2d0c-4f54-abbd-30b7dbe31021',
    'test deck 0',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  '690384af-2663-4fdb-b39e-84c85e11c81f',
    'test deck 1',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  'a1c13469-6636-4ee6-9424-9ef360d9053f',
    'test deck 2',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  '2e1dd210-e973-4975-b09f-4cad4994be73',
    'test deck 3',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  'ac192c6b-b5bc-488a-96d5-320295758a8b',
    'test deck 4',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  'c06f6800-827d-4fc6-a53f-13ef58bfb7d8',
    'test deck 5',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  '0c6e00ab-374f-4885-bf46-3f2d5c30c6fb',
    'test deck 6',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  '1e5c3449-589b-43e0-b927-f3935719726c',
    'test deck 7',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  'e5286a34-22bd-4864-8b71-11334bb2eb5e',
    'test deck 8',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  '1a3f1e2d-62e1-4e05-b4b1-c5d22a5afcce',
    'test deck 9',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  '5b47699f-b62b-4b65-a1bd-1284a0ef98a8',
    'test deck 10',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  '6926d964-9e6a-4066-872b-2029cb9a8bf6',
    'test deck 11',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  '9caae4c0-216e-4c75-b67f-d0d2542a608e',
    'test deck 12',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  'c198daec-7e61-4d09-a000-09e547a3d48d',
    'test deck 13',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  '28bb9c0a-aa2e-4f49-8a8a-e66a03b931c0',
    'test deck 14',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  'd1a759c5-74aa-4931-b9e9-3dd75d8632d7',
    'test deck 15',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  'ede64763-450c-4625-a419-b5855c28c51b',
    'test deck 16',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  'baae3411-6829-4b2f-b077-98c09ef1767e',
    'test deck 17',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  '97fd9b5c-dcd2-4c2b-9284-5aafe5a2f0ec',
    'test deck 18',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  '830640b4-5af1-47f0-ab7d-1dbab0729f91',
    'test deck 19',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  '6fb8dc63-c1bc-4fe7-bc7c-7b75f39e9852',
    'test deck 20',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  '935bd375-bf35-426f-bee1-9ba84bbf01d7',
    'test deck 21',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  'dda4c042-a98e-4a36-95e7-3f54fc27fcf6',
    'test deck 22',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  'eedfc507-7643-49df-af99-d576c85dd672',
    'test deck 23',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  ),
(
  '2498f116-d43c-4a47-bdc1-392d9d1ff564',
    'test deck 24',
    '0f8fad5b-d9cb-469f-a165-70867728950e',
    ''
  );

INSERT INTO topic (deck_id, tag_id) VALUES
  (
  '0037541d-2d0c-4f54-abbd-30b7dbe31021',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  '690384af-2663-4fdb-b39e-84c85e11c81f',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  'a1c13469-6636-4ee6-9424-9ef360d9053f',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  '2e1dd210-e973-4975-b09f-4cad4994be73',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  'ac192c6b-b5bc-488a-96d5-320295758a8b',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  'c06f6800-827d-4fc6-a53f-13ef58bfb7d8',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  '0c6e00ab-374f-4885-bf46-3f2d5c30c6fb',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  '1e5c3449-589b-43e0-b927-f3935719726c',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  'e5286a34-22bd-4864-8b71-11334bb2eb5e',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  '1a3f1e2d-62e1-4e05-b4b1-c5d22a5afcce',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  '5b47699f-b62b-4b65-a1bd-1284a0ef98a8',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  '6926d964-9e6a-4066-872b-2029cb9a8bf6',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  '9caae4c0-216e-4c75-b67f-d0d2542a608e',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  'c198daec-7e61-4d09-a000-09e547a3d48d',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  '28bb9c0a-aa2e-4f49-8a8a-e66a03b931c0',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  'd1a759c5-74aa-4931-b9e9-3dd75d8632d7',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  'ede64763-450c-4625-a419-b5855c28c51b',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  'baae3411-6829-4b2f-b077-98c09ef1767e',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  '97fd9b5c-dcd2-4c2b-9284-5aafe5a2f0ec',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  '830640b4-5af1-47f0-ab7d-1dbab0729f91',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  '6fb8dc63-c1bc-4fe7-bc7c-7b75f39e9852',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  '935bd375-bf35-426f-bee1-9ba84bbf01d7',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  'dda4c042-a98e-4a36-95e7-3f54fc27fcf6',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  'eedfc507-7643-49df-af99-d576c85dd672',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  ),
(
  '2498f116-d43c-4a47-bdc1-392d9d1ff564',
    '294d1c54-9b5b-428f-9bcc-4e6433b691f5'
  );
