-- -- Test Data
INSERT INTO public.account (account_id, username, password, avatar, timestamp) VALUES
  ('0f8fad5b-d9cb-469f-a165-70867728950e', 'john_doe', '$2y$10$Ya/zWJOE4bsmplhqh9VKkefUwFLvQKn89uxoKjF1UJFPzvlzOYtpm', 'a1b2c3d4', '2023-11-11T12:30:00'), -- password123
  ('7c9e6679-8b75-4d4b-a000-9c29efaa44b3', 'jane_smith', '$2y$10$wuUeddhloqkbjH8wNnrnJ.iCqyb4qFNlDnwv/gtDkm3ZfoPFwePq2', 'e5f6g7h8', '2023-11-11T12:35:00'), --securepass456
  ('f47ac10b-58cc-4372-a567-0e02b2c3d479', 'bob_jones', '$2y$10$xOVZRSB212nfqQRD.ExYMOFCDhHbfP2DMfayvVSrIVtZev2qrCVeO', 'i9j0k1l2', '2023-11-11T12:40:00'), -- 'myp@ssword
  ('d82f9c72-ff43-429a-886f-cc961b308e91', 'alice_green', '$2y$10$OSePb/DS4dOi6JpncitN8.3JqEo0OVzl8klROPVk143tragHpv0be', 'm3n4o5p6', '2023-11-11T12:45:00'), --strongPassword
  ('9c8e8f52-8a40-43d3-a050-482c3282adbc', 'sam_wilson', '$2y$10$5L0TnrxSFj248xcp9EyxzeA2RafYHBdGHW.8hut7qAJqASvdgtXBm', 'q7r8s9t0', '2023-11-11T12:50:00'), --pass1234
  ('edc1e69a-ff46-493f-bc44-3e875a21177d', 'lisa_miller', '$2y$10$jXCXLRb.9JMV7j9O3osdfeqYfdetEwl.oc2efIcwJbklVq4P5idxO', 'u1v2w3x4', '2023-11-11T12:55:00'), --password!23
  ('eb7a1c0e-522c-4f40-83e9-3b3b7ce34c65', 'michael_knight', '$2y$10$qTNP/BKEr9dt3JB/jGnQ9us7UDhDk7eRN7VggmxAVcb2Pkk02HlpS', 'y5z6a7b8', '2023-11-11T13:00:00'), --kn1ghtRider
  ('74316e29-57dd-43b6-8aae-92a03df6f8eb', 'emily_white', '$2y$10$LMWotrg5k7oa8/TWKCh3XO3lloFbFVh5TZkGarcbhcTpsQfbjFHrC', 'c9d0e1f2', '2023-11-11T13:05:00'), --p@ssw0rd
  ('4654a13b-0909-45c6-bc5a-674a0793e63b', 'ryan_black', '$2y$10$uc1YgA2UPmx5JFa3wHoiLOJa7Av2K8oxMkXYgvo4Z4vBDsyMWHeEy', 'g3h4i5j6', '2023-11-11T13:10:00'), --blackp@ss
  ('c5c83c5b-7e83-4935-8a67-58939225a1e9', 'susan_blue', '$2y$10$gakDNXY4s4vAOecxAfZaHuH0kjLTNBvRaFn9GGHteX5mzKbbqgH3C', 'k7l8m9n0', '2023-11-11T13:15:00');-- 

INSERT INTO public.deck (deck_id, account_id, title, description, timestamp) VALUES
  ('f8d6f63a-3d3d-429e-bd9a-7743f4b111e1', '0f8fad5b-d9cb-469f-a165-70867728950e', 'History Flashcards', 'Flashcards about historical events', '2023-11-11T14:30:00'),
  ('d570c9c5-1342-4c5d-b82a-2491f45008b3', 'd82f9c72-ff43-429a-886f-cc961b308e91', 'Mathematics Fundamentals', 'Fundamental concepts in mathematics', '2023-11-11T14:35:00'),
  ('ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9', '7c9e6679-8b75-4d4b-a000-9c29efaa44b3', 'Literature Masterpieces', 'Flashcards about classic literature', '2023-11-11T14:40:00'),
  ('bdf8c6ef-38fb-4c35-b103-b96a47a0e4da', 'eb7a1c0e-522c-4f40-83e9-3b3b7ce34c65', 'Chemistry Basics', 'Basic concepts in chemistry', '2023-11-11T14:45:00'),
  ('9b628635-bf41-4cb5-86d1-9c8c754c46b1', '9c8e8f52-8a40-43d3-a050-482c3282adbc', 'Spanish Vocabulary', 'Flashcards to build Spanish vocabulary', '2023-11-11T14:50:00'),
  ('4f5c9977-35ee-4dcd-953d-d2c4ba17be38', 'edc1e69a-ff46-493f-bc44-3e875a21177d', 'Programming Concepts', 'Fundamental concepts in programming', '2023-11-11T14:55:00'),
  ('b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b', '0f8fad5b-d9cb-469f-a165-70867728950e', 'Health and Fitness Tips', 'Tips for a healthy lifestyle', '2023-11-11T15:00:00'),
  ('15e07d6f-207c-4747-b06b-3d9cb1de1d8d', '9c8e8f52-8a40-43d3-a050-482c3282adbc', 'Art History Highlights', 'Flashcards about famous artworks and artists', '2023-11-11T15:05:00');

INSERT INTO public.card (deck_id, question, answer) VALUES
  ('f8d6f63a-3d3d-429e-bd9a-7743f4b111e1', 'What year did World War II end?', '1945'),
  ('f8d6f63a-3d3d-429e-bd9a-7743f4b111e1', 'Who was the U.S. president during World War II?', 'Franklin D. Roosevelt'),
  ('f8d6f63a-3d3d-429e-bd9a-7743f4b111e1', 'country was the first to use atomic bombs in warfare?', 'United States'),
  ( 'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1', 'the chemical symbol for uranium?', 'U'),
  ( 'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1', 'In  year did the Cold War end?', '1991'),
  ( 'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1', 'the capital of Germany?', 'Berlin'),
  ('f8d6f63a-3d3d-429e-bd9a-7743f4b111e1', 'Who wrote "To Kill a Mockingbird"?', 'Harper Lee'),
  ( 'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1', 'city hosted the 2016 Summer Olympics?', 'Rio de Janeiro'),
  ('d570c9c5-1342-4c5d-b82a-2491f45008b3', 'the value of pi (π)?', '3.14159'),
  ( 'd570c9c5-1342-4c5d-b82a-2491f45008b3', 'known as the "Father of Mathematics"?', 'Archimedes'),
  ('d570c9c5-1342-4c5d-b82a-2491f45008b3', 'the Pythagorean theorem?', 'a² + b² = c²'),
  ( 'd570c9c5-1342-4c5d-b82a-2491f45008b3', 'the sum of the angles in a triangle?', '180 degrees'),
  ( 'd570c9c5-1342-4c5d-b82a-2491f45008b3', 'Who developed the laws of motion?', 'Isaac Newton'),
  ( 'd570c9c5-1342-4c5d-b82a-2491f45008b3', 'the capital of France?', 'Paris'),
  ( 'd570c9c5-1342-4c5d-b82a-2491f45008b3', 'Who wrote "The Hitchhikers Guide to the Galaxy"?', 'Douglas Adams'),
  ('ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9', 'Who wrote "Romeo and Juliet"?', 'William Shakespeare'),
  ( 'ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9', 'In  century did the Renaissance begin?', '14th century'),
  ('ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9', 'Who painted the Mona Lisa?', 'Leonardo da Vinci'),
  ( 'ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9', 'In  city is the Louvre Museum located?', 'Paris'),
  ( 'ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9', 'considered the father of modern physics?', 'Albert Einstein'),
  ( 'ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9', 'the theory of relativity?', 'E=mc²'),
  ( 'ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9', 'known as the "Bard of Avon"?', 'William Shakespeare'),
  ( 'ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9', 'the meaning of the term "Renaissance"?', 'Rebirth'),
  ('bdf8c6ef-38fb-4c35-b103-b96a47a0e4da', 'the chemical symbol for gold?', 'Au'),
  ( 'bdf8c6ef-38fb-4c35-b103-b96a47a0e4da', 'a chemical element?', 'A substance that cannot be broken down into simpler substances by chemical means'),
   ('bdf8c6ef-38fb-4c35-b103-b96a47a0e4da', 'the most abundant gas in Earths atmosphere?', 'Nitrogen'),
  ( 'bdf8c6ef-38fb-4c35-b103-b96a47a0e4da', 'the chemical formula for water?', 'H₂O'),
  ( 'bdf8c6ef-38fb-4c35-b103-b96a47a0e4da', 'a chemical reaction?', 'The process by  substances are transformed into different substances'),
  ( 'bdf8c6ef-38fb-4c35-b103-b96a47a0e4da', 'the pH scale used for?', 'Measuring the acidity or basicity of a solution'),
  ( 'bdf8c6ef-38fb-4c35-b103-b96a47a0e4da', 'known as the "Father of Chemistry"?', 'Antoine Lavoisier'),
  ( 'bdf8c6ef-38fb-4c35-b103-b96a47a0e4da', 'an isotope?', 'Atoms of the same element with different numbers of neutrons'),
  ('9b628635-bf41-4cb5-86d1-9c8c754c46b1', 'How do you say "hello" in Spanish?', 'Hola'),
  ( '9b628635-bf41-4cb5-86d1-9c8c754c46b1', 'the capital of Spain?', 'Madrid'),
   ('9b628635-bf41-4cb5-86d1-9c8c754c46b1', 'a famous Spanish artist known for his surrealist paintings?', 'Salvador Dalí'),
  ( '9b628635-bf41-4cb5-86d1-9c8c754c46b1', 'the currency of Spain?', 'Euro'),
  ( '9b628635-bf41-4cb5-86d1-9c8c754c46b1', 'spanish dish consisting of rice, saffron, meats and vegetables?', 'Paella'),
  ( '9b628635-bf41-4cb5-86d1-9c8c754c46b1', 'Spanish architect known for his unique and avant-garde designs?', 'Antoni Gaudí'),
  ( '9b628635-bf41-4cb5-86d1-9c8c754c46b1', 'the largest city in Spain?', 'Madrid'),
  ( '4f5c9977-35ee-4dcd-953d-d2c4ba17be38', 'a loop in programming?', 'A sequence of instructions that is continually repeated until a certain condition is met'),
  ('4f5c9977-35ee-4dcd-953d-d2c4ba17be38', 'the difference between "for" and "while" loops?', '"For" loops are used when the number of iterations is known, while "while" loops are used when the condition for termination is not known initially'),
  ( '4f5c9977-35ee-4dcd-953d-d2c4ba17be38', 'the purpose of conditional statements in programming?', 'To make decisions and execute different code blocks based on specific conditions'),
  ( '4f5c9977-35ee-4dcd-953d-d2c4ba17be38', 'an array?', 'A data structure that stores a collection of elements, each identified by an index or a key'),
  ( '4f5c9977-35ee-4dcd-953d-d2c4ba17be38', 'the difference between a function and a method in programming?', 'A function is a block of code that performs a specific task, while a method is a function associated with an object'),
  ( '4f5c9977-35ee-4dcd-953d-d2c4ba17be38', 'the purpose of comments in code?', 'To provide explanations or additional information for human readers and developers'),
  ( '4f5c9977-35ee-4dcd-953d-d2c4ba17be38', 'the concept of "scope" in programming?', 'The region of the program where a variable can be accessed'),
( '4f5c9977-35ee-4dcd-953d-d2c4ba17be38', 'the role of a version control system in software development?', 'To track changes in the source code and coordinate work among multiple developers'),
( 'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b', 'What are some tips for a healthy lifestyle?', 'Exercise regularly, eat a balanced diet, get enough sleep'),
('b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b', 'How many hours of sleep are recommended for adults?', '7-9 hours'),
( 'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b', 'the importance of staying hydrated?', 'It helps maintain bodily functions, regulate temperature, and support overall health'),
( 'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b', 'the recommended daily intake of fruits and vegetables?', '5 servings'),
( 'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b', 'How does regular exercise benefit mental health?', 'It reduces stress, improves mood, and enhances cognitive function'),
( 'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b', 'the significance of a balanced diet?', 'It provides essential nutrients for the body to function properly'),
( 'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b', 'Why is it important to manage stress?', 'High stress levels can negatively impact physical and mental health'),
( 'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b', 'What are some benefits of adequate sleep?', 'Improved concentration, mood, and overall well-being'),
( '15e07d6f-207c-4747-b06b-3d9cb1de1d8d', 'Who painted the Mona Lisa?', 'Leonardo da Vinci'),
('15e07d6f-207c-4747-b06b-3d9cb1de1d8d', 'In  century did the Renaissance occur?', '14th to 17th century'),
( '15e07d6f-207c-4747-b06b-3d9cb1de1d8d', 'the significance of the Sistine Chapel ceiling?', 'Painted by Michelangelo, it is considered a masterpiece of Renaissance art'),
 ('15e07d6f-207c-4747-b06b-3d9cb1de1d8d', 'Who sculpted the statue of David?', 'Michelangelo'),
( '15e07d6f-207c-4747-b06b-3d9cb1de1d8d', 'artist known for realistic portrayals rural life?', 'Pieter Bruegel the Elder'),
( '15e07d6f-207c-4747-b06b-3d9cb1de1d8d', 'chiaroscuro in art?', 'The use of strong contrasts between light and dark to create a sense of volume and three-dimensionality'),
( '15e07d6f-207c-4747-b06b-3d9cb1de1d8d', 'considered the founder of Impressionism?', 'Claude Monet'),
( '15e07d6f-207c-4747-b06b-3d9cb1de1d8d', 'surrealism in art?', 'A 20th-century avant-garde movement that sought to release the creative potential of the unconscious mind');

INSERT INTO public.tag (tag_id, title) VALUES
  ('3e25960a-58c6-4f01-81f3-2c7a6c0a1111', 'History'),
  ('ec59aaeb-54f0-4c81-99e1-dc94de9e2222', 'Mathematics'),
  ('b91cbbb1-cc6a-4e0b-a788-133e071f3333', 'Literature'),
  ('ce072a37-051f-4a6b-87b3-1ef236734444', 'Science'),
  ('e98643b1-83cf-4f21-9361-798c49c55555', 'Language'),
  ('6f6c6887-7070-45ef-95a2-9b53b2356666', 'Programming'),
  ('bd822ac2-1ae7-4daa-8f15-80989ff77777', 'Health'),
  ('67ea0bc3-13c9-4bb3-b6eb-8ef48a288888', 'Art'),
  ('b512d3e5-9e29-4b3d-98bb-d3aa10599999', 'Technology'),
  ('24a4f0b9-0d12-4e62-831b-5b6ce9671010', 'Environment'),
  ('727a0b1b-60ff-4b6d-8a15-ba7a43212121', 'Python'),
  ('bb7a6dd4-c2e2-4e79-8140-ebc0e4f31313', 'Geography'),
  ('5558d622-6940-4c39-8d8e-6972a7a11414', 'Economics'),
  ('ff5260d2-0eb5-4b8c-99d0-9d62efb51515', 'Music'),
  ('c77f5f63-47a7-46d5-88d1-419a98e51616', 'French');

INSERT INTO public.topic (deck_id, tag_id) VALUES
  ('f8d6f63a-3d3d-429e-bd9a-7743f4b111e1', '3e25960a-58c6-4f01-81f3-2c7a6c0a1111'), 
  ('d570c9c5-1342-4c5d-b82a-2491f45008b3', 'ec59aaeb-54f0-4c81-99e1-dc94de9e2222'), 
  ('ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9', 'b91cbbb1-cc6a-4e0b-a788-133e071f3333'), 
  ('bdf8c6ef-38fb-4c35-b103-b96a47a0e4da', 'ce072a37-051f-4a6b-87b3-1ef236734444'), 
  ('9b628635-bf41-4cb5-86d1-9c8c754c46b1', 'e98643b1-83cf-4f21-9361-798c49c55555'), 
  ('4f5c9977-35ee-4dcd-953d-d2c4ba17be38', '6f6c6887-7070-45ef-95a2-9b53b2356666'), 
  ('b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b', 'bd822ac2-1ae7-4daa-8f15-80989ff77777'), 
  ('15e07d6f-207c-4747-b06b-3d9cb1de1d8d', '67ea0bc3-13c9-4bb3-b6eb-8ef48a288888');

INSERT INTO public.follow (account_id, tag_id) VALUES
  ('0f8fad5b-d9cb-469f-a165-70867728950e', '3e25960a-58c6-4f01-81f3-2c7a6c0a1111'), 
  ('0f8fad5b-d9cb-469f-a165-70867728950e', 'b91cbbb1-cc6a-4e0b-a788-133e071f3333'), 
  ('f47ac10b-58cc-4372-a567-0e02b2c3d479', 'ec59aaeb-54f0-4c81-99e1-dc94de9e2222'), 
  ('f47ac10b-58cc-4372-a567-0e02b2c3d479', 'e98643b1-83cf-4f21-9361-798c49c55555'), 
  ('f47ac10b-58cc-4372-a567-0e02b2c3d479', 'ce072a37-051f-4a6b-87b3-1ef236734444'), 
  ('f47ac10b-58cc-4372-a567-0e02b2c3d479', '6f6c6887-7070-45ef-95a2-9b53b2356666'), 
  ('d82f9c72-ff43-429a-886f-cc961b308e91', 'e98643b1-83cf-4f21-9361-798c49c55555'), 
  ('d82f9c72-ff43-429a-886f-cc961b308e91', 'bd822ac2-1ae7-4daa-8f15-80989ff77777'), 
  ('9c8e8f52-8a40-43d3-a050-482c3282adbc', '67ea0bc3-13c9-4bb3-b6eb-8ef48a288888'), 
  ('9c8e8f52-8a40-43d3-a050-482c3282adbc', 'b512d3e5-9e29-4b3d-98bb-d3aa10599999'), 
  ('edc1e69a-ff46-493f-bc44-3e875a21177d', '6f6c6887-7070-45ef-95a2-9b53b2356666'), 
  ('eb7a1c0e-522c-4f40-83e9-3b3b7ce34c65', 'bd822ac2-1ae7-4daa-8f15-80989ff77777'), 
  ('eb7a1c0e-522c-4f40-83e9-3b3b7ce34c65', '67ea0bc3-13c9-4bb3-b6eb-8ef48a288888'), 
  ('74316e29-57dd-43b6-8aae-92a03df6f8eb', 'e98643b1-83cf-4f21-9361-798c49c55555'), 
  ('4654a13b-0909-45c6-bc5a-674a0793e63b', 'ce072a37-051f-4a6b-87b3-1ef236734444'), 
  ('4654a13b-0909-45c6-bc5a-674a0793e63b', '24a4f0b9-0d12-4e62-831b-5b6ce9671010'), 
  ('c5c83c5b-7e83-4935-8a67-58939225a1e9', 'b512d3e5-9e29-4b3d-98bb-d3aa10599999'), 
  ('c5c83c5b-7e83-4935-8a67-58939225a1e9', 'ce072a37-051f-4a6b-87b3-1ef236734444'); 

INSERT INTO public.save (account_id, deck_id) VALUES
  ('0f8fad5b-d9cb-469f-a165-70867728950e', 'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1'), 
  ('7c9e6679-8b75-4d4b-a000-9c29efaa44b3', 'd570c9c5-1342-4c5d-b82a-2491f45008b3'), 
  ('f47ac10b-58cc-4372-a567-0e02b2c3d479', 'ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9'), 
  ('d82f9c72-ff43-429a-886f-cc961b308e91', 'bdf8c6ef-38fb-4c35-b103-b96a47a0e4da'), 
  ('9c8e8f52-8a40-43d3-a050-482c3282adbc', '9b628635-bf41-4cb5-86d1-9c8c754c46b1'), 
  ('edc1e69a-ff46-493f-bc44-3e875a21177d', '4f5c9977-35ee-4dcd-953d-d2c4ba17be38'), 
  ('eb7a1c0e-522c-4f40-83e9-3b3b7ce34c65', 'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b'), 
  ('74316e29-57dd-43b6-8aae-92a03df6f8eb', '15e07d6f-207c-4747-b06b-3d9cb1de1d8d'); 

INSERT INTO public.play (play_id, account_id, deck_id, score) VALUES
  ('4e4bfcc9-13cd-4e2a-b2c4-c0af4d2e1111', '0f8fad5b-d9cb-469f-a165-70867728950e', 'f8d6f63a-3d3d-429e-bd9a-7743f4b111e1', 80),
  ('50c1c87d-b2e7-47c2-9ed2-d076d1c32222', '7c9e6679-8b75-4d4b-a000-9c29efaa44b3', 'd570c9c5-1342-4c5d-b82a-2491f45008b3', 95),
  ('5c38fddb-bf39-43e7-af61-853924111333', 'f47ac10b-58cc-4372-a567-0e02b2c3d479', 'ef9989bf-0cf1-4713-9ab3-4ac1f6d7a0d9', 75),
  ('62e6c333-2e25-4b7f-b26b-868898555555', 'd82f9c72-ff43-429a-886f-cc961b308e91', 'bdf8c6ef-38fb-4c35-b103-b96a47a0e4da', 90),
  ('6d839522-7c45-4fe7-a6e1-620ce56b6b66', '9c8e8f52-8a40-43d3-a050-482c3282adbc', '9b628635-bf41-4cb5-86d1-9c8c754c46b1', 85),
  ('7dd5816e-9e8a-4b0d-a7c4-8fb550aaf111', 'edc1e69a-ff46-493f-bc44-3e875a21177d', '4f5c9977-35ee-4dcd-953d-d2c4ba17be38', 70),
  ('838aa4ae-736c-4918-8e9d-2e4c1c116688', 'eb7a1c0e-522c-4f40-83e9-3b3b7ce34c65', 'b1c4c4ac-01d0-4e35-b6c8-0c49ebf8f68b', 88),
  ('8cf41a0f-2334-4c06-926c-d2f8ea111999', '74316e29-57dd-43b6-8aae-92a03df6f8eb', '15e07d6f-207c-4747-b06b-3d9cb1de1d8d', 92);