DELETE FROM account;
DELETE FROM deck;
DELETE FROM tag;
DELETE FROM topic;

INSERT INTO account (account_id, username, password, avatar, timestamp) VALUES
	('a3511a35-6f9e-4147-bc11-3bd3b581efa1', 'John_Doe',  '$2y$10$ftxZhQIQZowmfXd6LKiQD.8NbvVcKLL8yowMKdORkmUVohG55HRlS', '1a3f9b7c', '2023-10-22 15:30:00'),
	('67e70953-55a0-4beb-9a97-6ff17af07691', 'Emily_Smith',  '$2y$10$SJK6Cv3hDHDmE9XMSV5hYudWdnUWzFxJIe6hH55616qV38Be1BOzC', 'e8d24f6a', '2023-11-11 12:30:00'),	
	('96dcf4e0-4095-43df-955b-8bfa54baaf97', 'Sarah_Carter',  '$2y$10$qgS6BKQkpjAlv.NMsiSqR.ztzK2BSBoAsVpbhiMz/BCriTOPh8iCe', 'b0f63e2d', '2023-10-14 12:00:00');
	
INSERT INTO deck (deck_id, account_id, title, description, timestamp) VALUES 
	('20630b8d-7d55-486f-8070-1c0f4ff2fca6', '67e70953-55a0-4beb-9a97-6ff17af07691', 'Security Risks and Precautions', 'Remind yourself of the different simple attacks against your program and methods to prevent them.', '2023-11-30 12:00:00'),
	('ce97ac0f-8819-4eea-bc3d-db55ba31c7c3', '96dcf4e0-4095-43df-955b-8bfa54baaf97', 'Binary Basics', 'The number system used by all computers. Commit the basics and representation of datatypes to your long term memory. ', '2023-11-02 12:30:00'),
	('ff0678dd-2331-4615-b6e8-230614961dd2', '67e70953-55a0-4beb-9a97-6ff17af07691', 'Newtons Laws', 'The fundamental laws of motion in physics. Practice answering the laws and what they are used for.', '2023-11-03 12:30:00');
	
INSERT INTO tag(tag_id, title) VALUES 
	('71def6cd-572f-4ecd-8a30-d51edba89d2a', 'Maths'),
	('250fbc41-10c0-4b10-88ea-bf91c52f9918', 'Physics'),
	('4e06f05f-16e1-4930-adca-355454466cee', 'Computing Science'),
	('b5380134-4263-4c19-a0d1-37a0c5f3912e', 'Chemistry');
	
INSERT INTO topic (tag_id, deck_id) VALUES 
	(
		'250fbc41-10c0-4b10-88ea-bf91c52f9918', 'ff0678dd-2331-4615-b6e8-230614961dd2'
	),
	(
		'4e06f05f-16e1-4930-adca-355454466cee', 'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3'
	),
	(
		'71def6cd-572f-4ecd-8a30-d51edba89d2a', 'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3'
	);
	
INSERT INTO likes (tag_id, user_id) VALUES
	('71def6cd-572f-4ecd-8a30-d51edba89d2a', 'a3511a35-6f9e-4147-bc11-3bd3b581efa1'),
	('4e06f05f-16e1-4930-adca-355454466cee', 'a3511a35-6f9e-4147-bc11-3bd3b581efa1'),
	('4e06f05f-16e1-4930-adca-355454466cee', '67e70953-55a0-4beb-9a97-6ff17af07691');
	

INSERT INTO save (user_id, deck_id) VALUES
	('a3511a35-6f9e-4147-bc11-3bd3b581efa1', 'ff0678dd-2331-4615-b6e8-230614961dd2'),
	('a3511a35-6f9e-4147-bc11-3bd3b581efa1', 'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3'),
	('96dcf4e0-4095-43df-955b-8bfa54baaf97', 'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3');
	
INSERT INTO play (user_id, deck_id, score, timestamp) VALUES
	('67e70953-55a0-4beb-9a97-6ff17af07691', 'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3', 7, '02/11/2023 12:30:00'),
	('67e70953-55a0-4beb-9a97-6ff17af07691', 'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3', 8, '19/11/2023 12:30:00'),
	('67e70953-55a0-4beb-9a97-6ff17af07691', 'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3', 11, '16/11/2023 12:30:00'),
	('67e70953-55a0-4beb-9a97-6ff17af07691', 'ff0678dd-2331-4615-b6e8-230614961dd2', 4, '23/11/2023 02:30:00'),
	('96dcf4e0-4095-43df-955b-8bfa54baaf97', 'ff0678dd-2331-4615-b6e8-230614961dd2', 4, '31/11/2023 02:30:00'),
	('96dcf4e0-4095-43df-955b-8bfa54baaf97', 'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3', 10, '12/11/2023 12:30:00'),
	('96dcf4e0-4095-43df-955b-8bfa54baaf97', 'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3', 12, '13/11/2023 12:30:00'),
	('96dcf4e0-4095-43df-955b-8bfa54baaf97', 'ff0678dd-2331-4615-b6e8-230614961dd2', 7, '22/11/2023 02:30:00');
	
INSERT INTO question (question_id, deck_id, question, answer) VALUES
	('4191bb49-94c9-45ad-828d-49d01780f6e4', '20630b8d-7d55-486f-8070-1c0f4ff2fca6', 'Firewall', 'Control network use'),
	('d3214777-f504-48b1-96a2-21ee0917dde2', '20630b8d-7d55-486f-8070-1c0f4ff2fca6', 'XSS Attack', 'Inject script tags into other users machines getting to run code on their browser'),
	('efda5e2c-2211-4a18-b387-30f3195bb17c', '20630b8d-7d55-486f-8070-1c0f4ff2fca6', 'SQL Injection', 'Injecting sql into database as string input is not sanitised '),
	('2cde1440-38e0-4a6e-9de1-15326308f7b3', '20630b8d-7d55-486f-8070-1c0f4ff2fca6', 'DOS attack', 'Spam requests to overload system'),
	('c002a33c-f0f4-4f8b-81d6-685600f986e2', '20630b8d-7d55-486f-8070-1c0f4ff2fca6', 'Prevent XSS Attack', 'Use htmlspecialchars when outputting'),
	('9d36af5f-1883-4a30-a60e-6cebd5fcea55', '20630b8d-7d55-486f-8070-1c0f4ff2fca6', 'Prevent SQL Injection', 'Use prepared statements'),
	('cab96744-15d0-4435-bdb6-5a392c7b8d78', '20630b8d-7d55-486f-8070-1c0f4ff2fca6', 'Prevent DOS attack', 'Timeout on lots of requests'),
	('ad11d162-24a5-40f4-ab05-fb004a532be3', '20630b8d-7d55-486f-8070-1c0f4ff2fca6', 'Encryption ', 'Scrambles the data so even if attacker gets it, they cannot read'),
	('f725745c-81af-4b08-8e3f-803aacae780c', '20630b8d-7d55-486f-8070-1c0f4ff2fca6', 'Private Key', 'A key that is used to decrypt data'),
	('a0670d92-f911-4f01-b8d4-bad8ffe68d8b', '20630b8d-7d55-486f-8070-1c0f4ff2fca6', 'Public key', 'A key used to encrypt data'),
	('2737831d-cd3f-4a76-9974-f0a2f603ef14', '20630b8d-7d55-486f-8070-1c0f4ff2fca6', 'Digital Certificate', 'Verifies a user sending a message is who they claim'),
	('8396bf26-c8b4-4eb0-9386-05189fafba3e', '20630b8d-7d55-486f-8070-1c0f4ff2fca6', 'Digital Signatures', 'Verifies an electronic message or document is authentic'),
	('c3cead37-e1a6-427b-906b-1372fdb8168b', '20630b8d-7d55-486f-8070-1c0f4ff2fca6', 'Tracking Cookies', 'Used to track users across websites'),
	
	('0ab258f6-6721-4e34-bed3-1940745aba67', 'ff0678dd-2331-4615-b6e8-230614961dd2', 'Newtons 1st law', 'An object at rest remains at rest, and an object in motion remains in motion at constant speed and in a straight line unless acted on by an unbalanced force.'),
	('8da557c8-b6df-4961-b7ab-b697545a710d', 'ff0678dd-2331-4615-b6e8-230614961dd2', 'Newtons 2nd law', 'F = ma'),
	('337a5f16-86ba-490e-95bb-c4ae5611d943', 'ff0678dd-2331-4615-b6e8-230614961dd2', 'Newtons 3rd law', 'Whenever one object exerts a force on another object, the second object exerts an equal and opposite on the first.'),
	('2896c6d3-40d9-4617-a750-69ef21d60856', 'ff0678dd-2331-4615-b6e8-230614961dd2', 'Unit of force', 'Newtons'),
	('bc13f4e2-63c2-45d8-84af-8f061cd92752', 'ff0678dd-2331-4615-b6e8-230614961dd2', 'Unit of acceleration', 'Meters per second per second'),
	('c136cde9-2ad3-4af5-8ad3-35e21d5d12ad', 'ff0678dd-2331-4615-b6e8-230614961dd2', 'Unit of mass', 'Kilograms'),
	('8d343435-0c7a-4617-b9ce-bf74929ca196', 'ff0678dd-2331-4615-b6e8-230614961dd2', 'Rocket is propelled by', 'Newtons 3rd law'),
	('c4b56f27-adb2-440a-b2f2-8c77abcc97c2', 'ff0678dd-2331-4615-b6e8-230614961dd2', 'A spaceship doesn’t slow down because', 'Newtons 1st laws'),
	
	
	('b474e7c0-48b0-47f8-b593-fb110352dc57', 'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3', '', ''),
	('5bb3e3ea-f2cf-4a3a-be1a-3d1966cc66ee', 'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3', '', ''),
	('ad4cf1bc-75ec-451d-a9bc-8c44f494e220', 'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3', '', ''),
	('ad4cf1bc-75ec-451d-a9bc-8c44f494e220', 'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3', '', ''),
	('74cf35d5-2a8c-4b32-bd52-11d7ffefb86f', 'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3', '', ''),
	('e14ce74c-fc1f-40d0-bd60-187336e53df9', 'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3', '', ''),
	('29ae95ae-fc6f-4b51-9b96-43a28cffbd7e', 'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3', '', ''),
	('34421b81-18df-483e-93de-fd8f07a2ab80', 'ce97ac0f-8819-4eea-bc3d-db55ba31c7c3', '', '');
	
	
	
	