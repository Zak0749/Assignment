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
-- Table for a set of questions
CREATE TABLE public.deck(
  -- Primary key ID field randomly generated on creation
  deck_id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  -- Required foreign key to the account_id on the account table
  -- Will be deleted when parent account is deleted
  -- The account that created the deck
  account_id UUID NOT NULL REFERENCES public.account ON DELETE CASCADE,
  -- A required 32 long string
  -- The name of the set of questions
  title VARCHAR(32) NOT NULL,
  -- A required 256 long string
  -- An explanation given by the creator about the account
  description VARCHAR(256) NOT NULL,
  -- Date and time set when deck created
  -- When the user created their the deck
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Table to store one key value question
CREATE TABLE public.question(
  -- Primary key ID field randomly generated on creation
  question_id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  -- Required foreign key to the deck_id on the deck table
  -- Will be deleted when parent deck is deleted
  -- The deck that the question belongs to
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
      ) - timestamp
  ) -- Gets the day part of the difference between the current row's timestamp and previous rows or for the first row the current timestamp
FROM play
WHERE play.account_id = account_idpa LOOP -- If the user has broken their streak
  IF date_diff > 1 THEN exit;
-- Only want day differences of one as if it zero then same day
ELSIF date_diff = 1 THEN streak := streak + 1;
END IF;
END LOOP;
RETURN streak;
END $$ LANGUAGE plpgsql;
-- passwords are plain text of password and order added e.g. first one is password1 etc
INSERT INTO public.account (
    account_id,
    username,
    password,
    avatar,
    timestamp
  )
VALUES (
    '868eefa5-f494-4cb5-81fc-4c238d0087a9',
    'John_Doe',
    '$2y$10$ftxZhQIQZowmfXd6LKiQD.8NbvVcKLL8yowMKdORkmUVohG55HRlS',
    '1a3f9b7c',
    '2023-10-22 15:30:00'
  ),
  -- 
  (
    '13ec0796-2f37-43ba-9013-44295f167793',
    'Emily_Smith',
    '$2y$10$SJK6Cv3hDHDmE9XMSV5hYudWdnUWzFxJIe6hH55616qV38Be1BOzC',
    'e8d24f6a',
    '2023-11-11 12:30:00'
  ),
  (
    '0d87505f-a97c-45dd-b4c5-e6255c86363d',
    'Alex_Jones',
    '$2y$10$3Mw0C/DLc6IR3D4syNw23uxuSuf7.r3U3YHwO/Fwk2oe5B2jlzhuq',
    '5c7a8d91',
    '2023-10-28 20:15:00'
  ),
  (
    'd78dbe66-51f1-4065-b5cb-33d8c8f8be28',
    'Sarah_Carter',
    '$2y$10$qgS6BKQkpjAlv.NMsiSqR.ztzK2BSBoAsVpbhiMz/BCriTOPh8iCe',
    'b0f63e2d',
    '2023-10-14 12:00:00'
  ),
  (
    '5aafc41b-6a89-46e5-b889-058fa2f02072',
    'David_Miller',
    '$2y$10$jXnhveUlO43zhyDsDLiwtOpDpH2JVJV2bsaLeuMcDzCG5YmeEvURS',
    '9a84c5f7',
    '2023-10-31 18:20:00'
  ),
  (
    'a418927d-0894-4aa8-baef-a2c77cf6d8bd',
    'Olivia_Clark',
    '$2y$10$kAudVkxr2t0Alv4pb8ZRWO6lSYkn6UkzyobugBnxof2GKFej1wRTy',
    '3e6d7a8b',
    '2023-10-25 07:55:00'
  ),
  (
    '880e6429-5d92-41f0-82e4-a7f45b51f843',
    'Ethan_Taylor',
    '$2y$10$.v4prdIIGnvXLQMJC4nffOcaW6QvYDvIcGVaUyS0nvGh20N35LyHa',
    'f1c9204e',
    '2023-10-17 23:10:00'
  ),
  (
    '7644923d-928b-488d-9547-68d6370561ae',
    'Ava_Wilson',
    '$2y$10$zsqx01q0cwR2S4.5/h6oEu/ie5yO8hepgjGid5p0imbO4Uy8AoM/a',
    '2b5f8dca',
    '2023-10-29 14:40:00'
  ),
  (
    '68c99a2a-bdc3-437f-acc5-7699bf3da23f',
    'Liam_Brown',
    '$2y$10$SZPJZ7q3muy1ayrbshEkjumDL/D0a8PGsiAyHQic.vxgXn4YdoTdK',
    'd7a6f4e8',
    '2023-10-12 10:25:00'
  ),
  (
    '86f87bb6-8a53-4df9-94b0-e9c14f908355',
    'Grace_Martin',
    '$2y$10$QzKwxhmk7iPExKuQgbfQLOhz0ET54Jx3/IRULAp9aRXx2YpxmHrbG',
    '8c3b1f9d',
    '2023-10-20 17:05:00'
  );
INSERT INTO tag (tag_id, title)
VALUES (
    '09b6863a-3d36-4e6a-bac7-168cf9b84b36',
    'Maths'
  ),
  (
    'bc8d6c01-6231-4d68-bb03-3c28c4bc87d1',
    'Physics'
  ),
  (
    'ef1e9b9b-762b-4ff3-bca5-9c3bca65ea7c',
    'Chemistry'
  ),
  (
    'a6ebd3f4-8fe7-4920-889c-173c95f2a295',
    'Biology'
  ),
  (
    'd59820e8-54df-4c36-a2c3-2167b6f85ef2',
    'Computing Science'
  ),
  (
    'b0c0d594-58f5-47d3-90fe-7a1f29283f92',
    'Business'
  ),
  (
    '4b34da56-67d0-429a-8233-2e4051d0e2f1',
    'Administration'
  ),
  (
    '7e90a2f1-65c8-4ba6-8f7a-2e3a8357b91b',
    'History'
  ),
  (
    '4372a732-cb8b-46a8-b9a2-8c1a8e3d2b92',
    'Geography'
  ),
  (
    '51d89760-3e65-4f8d-8de9-f67da26895e1',
    'Modern Studies'
  ),
  (
    '7c0e3c6a-c0b5-4653-b243-8b6c235f23e1',
    'English'
  ),
  ('d8bb3e23-ea12-4e91-bb0f-5a1c4a1ba111', 'French'),
  ('54bdaaf2-07a0-4f8a-9a8c-287d642c3495', 'German'),
  ('ec2e8b08-b6a0-4fbb-90d9-18726c021e8c', 'Music'),
  ('f9911d03-6f39-45d3-b10c-fd08e2e65af0', 'Art'),
  (
    'ff1f9c3a-3cfe-4ec7-95e1-b690688f0c5a',
    'Graphic Communication'
  ),
  (
    '3d071417-24a2-48e5-98b0-4f956ea88333',
    'Engineering Science'
  ),
  (
    '134a21a0-8ff7-4b7a-8925-9ee4e7817f02',
    'Design and Manufacture'
  );
INSERT INTO follow (account_id, tag_id)
VALUES (
    '868eefa5-f494-4cb5-81fc-4c238d0087a9',
    '09b6863a-3d36-4e6a-bac7-168cf9b84b36'
  ),
  (
    '13ec0796-2f37-43ba-9013-44295f167793',
    'bc8d6c01-6231-4d68-bb03-3c28c4bc87d1'
  ),
  (
    '13ec0796-2f37-43ba-9013-44295f167793',
    'ef1e9b9b-762b-4ff3-bca5-9c3bca65ea7c'
  ),
  (
    '13ec0796-2f37-43ba-9013-44295f167793',
    'a6ebd3f4-8fe7-4920-889c-173c95f2a295'
  ),
  (
    '0d87505f-a97c-45dd-b4c5-e6255c86363d',
    'b0c0d594-58f5-47d3-90fe-7a1f29283f92'
  ),
  (
    '0d87505f-a97c-45dd-b4c5-e6255c86363d',
    '4b34da56-67d0-429a-8233-2e4051d0e2f1'
  ),
  (
    '5aafc41b-6a89-46e5-b889-058fa2f02072',
    'd59820e8-54df-4c36-a2c3-2167b6f85ef2'
  ),
  (
    '5aafc41b-6a89-46e5-b889-058fa2f02072',
    '09b6863a-3d36-4e6a-bac7-168cf9b84b36'
  ),
  (
    '5aafc41b-6a89-46e5-b889-058fa2f02072',
    'bc8d6c01-6231-4d68-bb03-3c28c4bc87d1'
  ),
  (
    '5aafc41b-6a89-46e5-b889-058fa2f02072',
    '3d071417-24a2-48e5-98b0-4f956ea88333'
  ),
  (
    'a418927d-0894-4aa8-baef-a2c77cf6d8bd',
    '7e90a2f1-65c8-4ba6-8f7a-2e3a8357b91b'
  ),
  (
    'a418927d-0894-4aa8-baef-a2c77cf6d8bd',
    '4372a732-cb8b-46a8-b9a2-8c1a8e3d2b92'
  ),
  (
    'a418927d-0894-4aa8-baef-a2c77cf6d8bd',
    '51d89760-3e65-4f8d-8de9-f67da26895e1'
  ),
  (
    'a418927d-0894-4aa8-baef-a2c77cf6d8bd',
    '7c0e3c6a-c0b5-4653-b243-8b6c235f23e1'
  ),
  (
    'a418927d-0894-4aa8-baef-a2c77cf6d8bd',
    '54bdaaf2-07a0-4f8a-9a8c-287d642c3495'
  ),
  (
    '880e6429-5d92-41f0-82e4-a7f45b51f843',
    'd8bb3e23-ea12-4e91-bb0f-5a1c4a1ba111'
  ),
  (
    '880e6429-5d92-41f0-82e4-a7f45b51f843',
    'ec2e8b08-b6a0-4fbb-90d9-18726c021e8c'
  ),
  (
    '880e6429-5d92-41f0-82e4-a7f45b51f843',
    'f9911d03-6f39-45d3-b10c-fd08e2e65af0'
  ),
  (
    '7644923d-928b-488d-9547-68d6370561ae',
    'ff1f9c3a-3cfe-4ec7-95e1-b690688f0c5a'
  ),
  (
    '7644923d-928b-488d-9547-68d6370561ae',
    '134a21a0-8ff7-4b7a-8925-9ee4e7817f02'
  ),
  (
    '68c99a2a-bdc3-437f-acc5-7699bf3da23f',
    'ec2e8b08-b6a0-4fbb-90d9-18726c021e8c'
  ),
  (
    '86f87bb6-8a53-4df9-94b0-e9c14f908355',
    '7c0e3c6a-c0b5-4653-b243-8b6c235f23e1'
  ),
  (
    '86f87bb6-8a53-4df9-94b0-e9c14f908355',
    '51d89760-3e65-4f8d-8de9-f67da26895e1'
  ),
  (
    '86f87bb6-8a53-4df9-94b0-e9c14f908355',
    'ef1e9b9b-762b-4ff3-bca5-9c3bca65ea7c'
  );
INSERT INTO deck (
    deck_id,
    account_id,
    title,
    description,
    timestamp
  )
VALUES (
    '3c036bcc-2d05-4f87-9c01-c1a01e8f32a7',
    '13ec0796-2f37-43ba-9013-44295f167793',
    'Scientific Uncertainties',
    'Delve into the heart of scientific inquiry with these flash cards on uncertainties. Explore the multifaceted aspects of uncertainty, from measurement errors to model limitations, and grasp the essential skills of communicating and navigating uncertainties in the dynamic world of scientific research.',
    '2023-11-20 15:45:27'
  ),
  (
    'f6e8f4d4-04f3-4d49-a3b1-398cb14c3dd1',
    '09b6863a-3d36-4e6a-bac7-168cf9b84b36',
    'Standard Derivatives',
    'Explore the fundamental concepts of standard derivatives with these flashcards. Dive into the world of calculus and understand the principles behind rates of change, tangents, and instantaneous rates.',
    '2023-12-05 10:30:15'
  ),
  (
    'bfc5e570-4e74-4b0a-a314-0c1a4cfc5edf',
    '0d87505f-a97c-45dd-b4c5-e6255c86363d',
    'Organizational Structures',
    'Gain insights into different organizational structures with these flashcards. From hierarchical to matrix structures, explore the advantages and disadvantages of each, and understand how they impact an organizations dynamics.',
    '2023-12-08 14:20:45'
  ),
  (
    '749e85a8-4c7e-4e91-b07d-47a04997d5b8',
    '5aafc41b-6a89-46e5-b889-058fa2f02072',
    'Binary Basics',
    'Master the fundamentals of binary systems with these flashcards. Learn how binary code is used to represent information in computers and understand the building blocks of digital technology.',
    '2023-12-12 16:45:30'
  ),
  (
    'd79d476c-6fc7-46ae-b47b-4d094c6db9ae',
    '5aafc41b-6a89-46e5-b889-058fa2f02072',
    'Forces in Physics',
    'Explore the world of forces in physics with these flashcards. From Newtons laws to gravitational forces, delve into the principles that govern motion and interactions between objects.',
    '2023-12-15 11:10:22'
  ),
  (
    '6b89a670-2b16-49a3-9f4c-4c88e46ea04c',
    'a418927d-0894-4aa8-baef-a2c77cf6d8bd',
    'German Greetings',
    'Learn essential German greetings and expressions with these flashcards. Perfect your conversational skills and immerse yourself in the basics of the German language.',
    '2023-12-18 09:55:18'
  ),
  (
    'aab6aa8d-9d49-40d4-8f55-93c47c2f79d2',
    'a418927d-0894-4aa8-baef-a2c77cf6d8bd',
    'Paragraph Structure',
    'Enhance your writing skills with these flashcards on paragraph structure. Follow the PEEL method (Point, Evidence, Explanation, Link) to create well-organized and coherent paragraphs.',
    '2023-12-22 13:25:50'
  ),
  (
    'e736ea45-1ba6-4c95-a746-8819510f8c6d',
    '880e6429-5d92-41f0-82e4-a7f45b51f843',
    'French Art',
    'Embark on a journey through French art with these flashcards. Discover prominent artists, art movements, and masterpieces that have shaped the rich cultural landscape of France.',
    '2023-12-25 17:40:12'
  ),
  (
    '7c66cb8c-3cc2-40d8-8f6d-0e594fd1944a',
    'd78dbe66-51f1-4065-b5cb-33d8c8f8be28',
    'Practical Woodwork',
    'Get hands-on with practical woodwork using these flashcards. Learn essential techniques, tools, and safety precautions for crafting woodwork projects.',
    '2023-12-28 19:15:37'
  ),
  (
    '2a5e6204-5a25-481a-994c-66f8bb4d0c61',
    '68c99a2a-bdc3-437f-acc5-7699bf3da23f',
    'Music Symbols',
    'Discover the language of music through these flashcards on music symbols. From notes and rests to dynamic markings, enhance your understanding of musical notation.',
    '2024-01-02 08:50:05'
  ),
  (
    '3d5839da-5d0a-4c38-8ec1-8bcac8de97e0',
    '86f87bb6-8a53-4df9-94b0-e9c14f908355',
    'Periodic Table',
    'Explore the elements of the periodic table with these flashcards. Understand the properties, atomic structures, and trends that define each element in the periodic system.',
    '2024-01-05 12:05:28'
  ), 
  (
    '3d5839da-5d0a-4c38-8ec1-8bcac8de97e0',
    '86f87bb6-8a53-4df9-94b0-e9c14f908355',
    'Security Risks and Precautions',
    'Dive into the world of cybersecurity with these flashcards on security risks and precautions. Understand common threats, vulnerabilities, and best practices to safeguard digital information.',
    '2024-01-08 14:30:50'
  );