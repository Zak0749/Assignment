DROP TABLE IF EXISTS User;
DROP TABLE IF EXISTS Deck;
DROP TABLE IF EXISTS Question;
DROP TABLE IF EXISTS Tag;
DROP TABLE IF EXISTS Deck_Topic;
DROP TABLE IF EXISTS User_Likes;
DROP TABLE IF EXISTS User_Save;
DROP TABLE IF EXISTS User_Play;
-- password no need hased as gonna be hexed
CREATE TABLE User(
    user_id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    avatar TEXT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE Deck(
    deck_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    featured BOOL NOT NULL DEFAULT 0,
    plays INT NOT NULL DEFAULT 0,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES User(user_id) ON DELETE CASCADE
);
CREATE TABLE Question(
    question_id INTEGER PRIMARY KEY AUTOINCREMENT,
    deck_id INTEGER NOT NULL,
    key TEXT NOT NULL,
    value TEXT NOT NULL,
    FOREIGN KEY(deck_id) REFERENCES Deck(deck_id) ON DELETE CASCADE
);
CREATE TABLE Tag(
    tag_id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL
);
CREATE TABLE Deck_Topic(
    deck_id INTEGER NOT NULL,
    tag_id INTEGER NOT NULL,
    FOREIGN KEY(deck_id) REFERENCES Deck(deck_id) ON DELETE CASCADE,
    FOREIGN KEY(tag_id) REFERENCES Tag(tag_id) ON DELETE CASCADE
);
CREATE TABLE User_Likes(
    user_id INTEGER NOT NULL,
    tag_id INTEGER NOT NULL,
    FOREIGN KEY(user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY(tag_id) REFERENCES Tag(tag_id) ON DELETE CASCADE
);
CREATE TABLE User_Save(
    user_id INTEGER NOT NULL,
    deck_id INTEGER NOT NULL,
    FOREIGN KEY(user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY(deck_id) REFERENCES Deck(deck_id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, deck_id)
);
CREATE TABLE User_Play(
    play_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    deck_id INTEGER NOT NULL,
    score INTEGER NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY(deck_id) REFERENCES Deck(deck_id) ON DELETE CASCADE
);
CREATE INDEX username_index ON User (username);
CREATE INDEX deck_title_index ON Deck (title);
CREATE INDEX tag_index ON Tag (title);
INSERT INTO User (
        username,
        avatar,
        password
    )
VALUES (
        'Zak',
        '1a45050d',
        '$2y$10$0rnzqHGDH7cb/U6ZQy5vguNbpzTJZABngTEsMF.k9.yhS5da6SlAC'
    ),
    (
        'Freya',
        '89b59c87',
        'password2'
    ),
    (
        'king_bob',
        '008d108e',
        'password3'
    ),
    (
        'Keeley',
        '610b0043',
        'password4'
    ),
    (
        'Finn',
        '52015ba9',
        'password5'
    ),
    (
        'Calum',
        'db9164c2',
        'password6'
    ),
    (
        'Adam',
        '35f3a987',
        'password7'
    ),
    (
        'Liam',
        '401011e3',
        'password8'
    ),
    (
        'Daniel',
        '5770b036',
        'password9'
    ),
    (
        'Alister',
        'cc48d2f9',
        'password10'
    ),
    (
        'Ryan',
        '516f5a52',
        'password11'
    ),
    (
        'Isla',
        '67469c0a',
        'password12'
    ),
    (
        'Micheal',
        'f7d95826',
        'password13'
    ),
    (
        'Conlan',
        '8a7aee66',
        'password14'
    ),
    (
        'Mrs_Ferguson',
        'fbc90a8d',
        'password15'
    ),
    (
        'Miss_Douglas',
        '78f49a8f',
        'password16'
    ),
    (
        'Mrs_Young',
        'cc74a4d5',
        'password17'
    ),
    (
        'Mr_Mcinnis',
        '0fb14900',
        'password18'
    ),
    (
        'Mr_Mercer',
        'af0e0b56',
        'password19'
    ),
    (
        'Mr_Clancy',
        'e0c3f8f0',
        'password20'
    ),
    (
        'Bob',
        '7cd57124',
        'password21'
    );
INSERT INTO Deck (
        user_id,
        title,
        description,
        featured,
        plays
    )
VALUES (
        1,
        'Science Facts',
        'Interesting science facts',
        1,
        120
    ),
    (
        2,
        'Vocabulary Builder',
        'Expand your vocabulary',
        0,
        75
    ),
    (
        3,
        'History Trivia',
        'Test your knowledge of historical events',
        1,
        90
    ),
    (
        3,
        'Math Challenges',
        'Sharpen your math skills',
        0,
        50
    ),
    (
        4,
        'Nature and Wildlife',
        'Learn about animals and ecosystems',
        1,
        105
    ),
    (
        5,
        'Word Puzzles',
        'Solve fun word puzzles',
        0,
        60
    ),
    (
        6,
        'Geography Quiz',
        'Explore the world with geography questions',
        1,
        70
    ),
    (
        6,
        'Art Appreciation',
        'Discover famous artworks and artists',
        0,
        40
    ),
    (
        6,
        'Literature Classics',
        'Explore classic literature works',
        0,
        25
    ),
    (
        7,
        'Music History',
        'Learn about the evolution of music',
        0,
        55
    ),
    (
        7,
        'Famous Quotes',
        'Guess the authors of famous quotes',
        1,
        80
    ),
    (
        8,
        'Astronomy Wonders',
        'Explore the universe and celestial bodies',
        0,
        65
    ),
    (
        9,
        'Healthy Living Tips',
        'Get insights into maintaining a healthy lifestyle',
        0,
        30
    ),
    (
        10,
        'World Cuisine',
        'Discover cuisines from around the globe',
        1,
        110
    ),
    (
        10,
        'Travel Destinations',
        'Plan your next travel adventure',
        0,
        45
    ),
    (
        10,
        'Movie Trivia',
        'Test your knowledge of popular movies',
        1,
        95
    ),
    (
        11,
        'Famous Inventors',
        'Learn about inventors who shaped history',
        0,
        70
    ),
    (
        11,
        'Language Learning',
        'Basic phrases in different languages',
        0,
        20
    ),
    (
        11,
        'Space Exploration',
        'Explore achievements in space exploration',
        0,
        35
    ),
    (
        12,
        'Sports Legends',
        'Celebrate iconic athletes and sports moments',
        1,
        85
    ),
    (
        13,
        'Environmental Issues',
        'Raise awareness about environmental challenges',
        0,
        40
    ),
    (
        14,
        'Mindful Meditation',
        'Practice mindfulness and meditation',
        1,
        75
    ),
    (
        14,
        'Tech Innovations',
        'Discover breakthroughs in technology',
        0,
        30
    ),
    (
        14,
        'Classic Novels',
        'Explore timeless literary works',
        0,
        25
    ),
    (
        15,
        'Animal Kingdom',
        'Learn about diverse animal species',
        0,
        50
    ),
    (
        16,
        'Physics Concepts',
        'Explore fundamental principles of physics',
        1,
        65
    ),
    (
        17,
        'Celebrities Trivia',
        'Test your knowledge of celebrities',
        0,
        55
    ),
    (
        18,
        'Gardening Tips',
        'Grow your own garden with these tips',
        1,
        90
    ),
    (
        18,
        'Cooking Techniques',
        'Master essential cooking methods',
        0,
        40
    ),
    (
        19,
        'Famous Landmarks',
        'Discover iconic landmarks from around the world',
        0,
        60
    );
INSERT INTO Question (deck_id, key, value)
VALUES (1, 'Speed of light', '3 x 10^8 m/s'),
    (1, 'Theory of Relativity', 'Albert Einstein'),
    (1, 'Electric Current Unit', 'Ampere (A)'),
    (1, 'SI Unit of Energy', 'Joule (J)'),
    (
        1,
        'Velocity Definition',
        'The rate of change of displacement with respect to time'
    ),
    (
        1,
        'Conservation of Energy Law',
        'Energy cannot be created or destroyed, only transformed'
    ),
    (
        1,
        'Newtons First Law',
        'An object at rest tends to stay at rest, and an object in motion tends to stay in motion unless acted upon by an external force'
    ),
    (
        1,
        'Electromagnetic Spectrum',
        'The range of all types of electromagnetic radiation'
    ),
    (
        1,
        'Work Done Formula',
        'Work (W) = Force (F) x Distance (d) x cos(θ)'
    ),
    (
        1,
        'Buoyancy Principle',
        'An object immersed in a fluid experiences an upward buoyant force equal to the weight of the fluid it displaces'
    ),
    (
        1,
        'Inertia Concept',
        'The tendency of an object to resist changes in its state of motion'
    ),
    (
        1,
        'Law of Reflection',
        'The angle of incidence is equal to the angle of reflection'
    ),
    (
        1,
        'Hookes Law Explanation',
        'The force exerted by a spring is directly proportional to the displacement of the spring from its equilibrium position'
    ),
    (
        1,
        'Speed vs. Velocity',
        'Speed is the magnitude of velocity without direction'
    ),
    (
        1,
        'Kinetic Energy Definition',
        'The energy of an object in motion'
    ),
    (
        1,
        'Conservation of Momentum Principle',
        'The total momentum of a closed system remains constant'
    ),
    (
        1,
        'Frequency-Wavelength Relationship',
        'Speed of light (c) = Frequency (f) x Wavelength (λ)'
    ),
    (
        1,
        'Electric Potential Energy',
        'The energy associated with the position of an electric charge in an electric field'
    ),
    (
        1,
        'Universal Gravitation Law',
        'Every point mass attracts every other point mass by a force acting along the line intersecting both points'
    ),
    (
        1,
        'Light Behavior in Prism',
        'Dispersion of light into its constituent colors due to refraction'
    );
INSERT INTO Question (deck_id, key, value)
VALUES (2, 'Word1', 'Ephemeral'),
    (2, 'Word2', 'Ubiquitous'),
    (2, 'Word3', 'Surreptitious');
INSERT INTO Question (deck_id, key, value)
VALUES (3, 'Q1', 'In which year did World War I end?'),
    (
        3,
        'Q2',
        'Who was the first president of the United States?'
    ),
    (
        3,
        'Q3',
        'Where was the Declaration of Independence signed?'
    );
INSERT INTO Question (deck_id, key, value)
VALUES (4, 'Q1', 'What is the value of pi (π)?'),
    (4, 'Q2', 'Solve for x: 2x + 5 = 15'),
    (4, 'Q3', 'What is the Pythagorean theorem?');
-- Questions for Nature and Wildlife deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        5,
        'Q1',
        'Which animal is known as the "king of the jungle"?'
    ),
    (5, 'Q2', 'What is the largest mammal on Earth?'),
    (
        5,
        'Q3',
        'What is the process of an animal shedding its outer layer called?'
    );
-- Questions for Word Puzzles deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        6,
        'Puzzle1',
        'I am a five-letter word. Take away two and I sound quite the same. What am I?'
    ),
    (
        6,
        'Puzzle2',
        'Im a word of letters three. Add two and fewer there will be. What am I?'
    ),
    (
        6,
        'Puzzle3',
        'What word becomes shorter when you add two letters to it?'
    );
INSERT INTO Question (deck_id, key, value)
VALUES (
        7,
        'Q1',
        'Which river is the longest in the world?'
    ),
    (7, 'Q2', 'What is the capital of Australia?'),
    (
        7,
        'Q3',
        'Which mountain range stretches across the western part of North America?'
    );
-- Questions for Art Appreciation deck
INSERT INTO Question (deck_id, key, value)
VALUES (8, 'Q1', 'Who painted the Mona Lisa?'),
    (
        8,
        'Q2',
        'Which art movement is known for its use of bright colors and bold shapes?'
    ),
    (8, 'Q3', 'Who sculpted the statue of David?');
-- Questions for Literature Classics deck
INSERT INTO Question (deck_id, key, value)
VALUES (9, 'Q1', 'Who wrote "Pride and Prejudice"?'),
    (
        9,
        'Q2',
        'Which novel features the character Holden Caulfield?'
    ),
    (
        9,
        'Q3',
        'What is the first book of J.R.R. Tolkiens "The Lord of the Rings" trilogy?'
    );
INSERT INTO Question (deck_id, key, value)
VALUES (
        10,
        'Q1',
        'Who composed the "Symphony No. 9" (Choral Symphony)?'
    ),
    (
        10,
        'Q2',
        'What musical genre originated in New Orleans, Louisiana?'
    ),
    (
        10,
        'Q3',
        'Which band released the album "Sgt. Peppers Lonely Hearts Club Band"?'
    );
-- Questions for Famous Quotes deck --
INSERT INTO Question (deck_id, key, value)
VALUES (
        11,
        'Quote1',
        'In the end, we will remember not the words of our enemies, but the silence of our friends.'
    ),
    (
        11,
        'Quote2',
        'The only way to do great work is to love what you do.'
    ),
    (
        11,
        'Quote3',
        'You miss 100% of the shots you dont take.'
    );
-- Questions for Astronomy Wonders deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        12,
        'Q1',
        'What is the name of the largest planet in our solar system?'
    ),
    (12, 'Q2', 'What is a light-year?'),
    (
        12,
        'Q3',
        'What type of celestial body is the center of a galaxy?'
    );
-- Questions for Healthy Living Tips deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        13,
        'Tip1',
        'Stay hydrated by drinking at least 8 glasses of water a day.'
    ),
    (
        13,
        'Tip2',
        'A balanced diet should include a variety of fruits, vegetables, and lean proteins.'
    ),
    (
        13,
        'Tip3',
        'Regular exercise helps improve cardiovascular health and reduces stress.'
    );
-- Questions for World Cuisine deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        14,
        'Q1',
        'Which country is known for inventing sushi?'
    ),
    (
        14,
        'Q2',
        'What spice is commonly used in Indian cuisine and gives curry its yellow color?'
    ),
    (
        14,
        'Q3',
        'What Italian dish consists of thinly sliced raw meat or fish?'
    );
-- Questions for Travel Destinations deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        15,
        'Q1',
        'Which city is known as the "City of Light"?'
    ),
    (
        15,
        'Q2',
        'What famous landmark is located in Rio de Janeiro, Brazil?'
    ),
    (
        15,
        'Q3',
        'In which country can you find the Great Barrier Reef?'
    );
-- Questions for Movie Trivia deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        16,
        'Q1',
        'What 1975 blockbuster film features a great white shark terrorizing a small beach town?'
    ),
    (
        16,
        'Q2',
        'Who directed the "Lord of the Rings" film trilogy?'
    ),
    (
        16,
        'Q3',
        'Which actor portrayed Tony Stark/Iron Man in the Marvel Cinematic Universe?'
    );
-- Questions for Famous Inventors deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        17,
        'Q1',
        'Who is credited with inventing the telephone?'
    ),
    (
        17,
        'Q2',
        'What scientist formulated the theory of relativity?'
    ),
    (
        17,
        'Q3',
        'Who developed the first practical electric light bulb?'
    );
-- Questions for Environmental Issues deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        18,
        'Q1',
        'What term describes the gradual increase in the Earths average temperature due to human activities?'
    ),
    (
        18,
        'Q2',
        'Which greenhouse gas is primarily responsible for global warming?'
    ),
    (
        18,
        'Q3',
        'What is the term for the loss of species in a specific habitat?'
    );
-- Questions for Mindful Meditation deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        19,
        'Q1',
        'What is the practice of focusing on the present moment called?'
    ),
    (
        19,
        'Q2',
        'What are some common techniques to promote relaxation and mindfulness?'
    ),
    (
        19,
        'Q3',
        'How can meditation contribute to stress reduction and mental well-being?'
    );
-- Questions for Tech Innovations deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        20,
        'Q1',
        'What is the term for a computer program that replicates itself and spreads to other computers?'
    ),
    (
        20,
        'Q2',
        'Which company developed the first commercially successful personal computer?'
    ),
    (
        20,
        'Q3',
        'What technology is often associated with the term "blockchain"?'
    );
-- Questions for Classic Novels deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        21,
        'Q1',
        'Who wrote "1984," a dystopian novel set in a totalitarian society?'
    ),
    (
        21,
        'Q2',
        'Which novel features the characters Elizabeth Bennet and Mr. Darcy?'
    ),
    (
        21,
        'Q3',
        'What is the main setting of "To Kill a Mockingbird" by Harper Lee?'
    );
-- Questions for Animal Kingdom deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        22,
        'Q1',
        'What is the largest species of big cat?'
    ),
    (
        22,
        'Q2',
        'Which animal is known as the "king of the beasts"?'
    ),
    (
        22,
        'Q3',
        'What type of animal belongs to the order Rodentia?'
    );
-- Questions for Physics Concepts deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        23,
        'Q1',
        'What law states that for every action, there is an equal and opposite reaction?'
    ),
    (
        23,
        'Q2',
        'What is the basic unit of electric charge?'
    ),
    (
        23,
        'Q3',
        'What is the force that resists the motion of objects through a fluid called?'
    );
-- Questions for Celebrities Trivia deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        24,
        'Q1',
        'Which actor played the role of Tony Stark/Iron Man in the Marvel Cinematic Universe?'
    ),
    (
        24,
        'Q2',
        'What famous actress starred in the movie "Pretty Woman"?'
    ),
    (
        24,
        'Q3',
        'Who is known for his role as Jack Dawson in the film "Titanic"?'
    );
-- Questions for Gardening Tips deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        25,
        'Tip1',
        'What is the process of removing dead or overgrown branches from plants called?'
    ),
    (
        25,
        'Tip2',
        'Which type of soil retains water well and is suitable for many plants?'
    ),
    (
        25,
        'Tip3',
        'What is the practice of growing plants without soil, usually in a nutrient-rich solution?'
    );
-- Questions for Cooking Techniques deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        26,
        'Technique1',
        'What cooking technique involves submerging food in hot oil to cook it quickly?'
    ),
    (
        26,
        'Technique2',
        'What is the technique of cooking food using dry heat in an oven called?'
    ),
    (
        26,
        'Technique3',
        'What cooking technique involves simmering food in a flavorful liquid?'
    );
-- Questions for Famous Landmarks deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        27,
        'Q1',
        'What ancient wonder is known for its colossal statues of human heads?'
    ),
    (
        27,
        'Q2',
        'Which iconic bridge connects the city of San Francisco to Marin County?'
    ),
    (
        27,
        'Q3',
        'What historical site features a complex of massive stone temples in Cambodia?'
    );
-- Questions for Meditation Techniques deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        28,
        'Technique1',
        'What is a common focus point in mindfulness meditation?'
    ),
    (
        28,
        'Technique2',
        'What type of meditation involves chanting a mantra or focusing on breath?'
    ),
    (
        28,
        'Technique3',
        'What is the practice of walking slowly and mindfully, often in a circle?'
    );
-- Questions for Science Fiction Literature deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        29,
        'Q1',
        'Who authored the science fiction novel "Dune"?'
    ),
    (
        29,
        'Q2',
        'In which dystopian novel does the protagonist encounter the "Ministry of Truth"?'
    ),
    (
        29,
        'Q3',
        'What is the title of Isaac Asimovs science fiction collection of short stories?'
    );
-- Questions for Mind-Body Connection deck
INSERT INTO Question (deck_id, key, value)
VALUES (
        30,
        'Q1',
        'What term refers to the practice of focusing on both mental and physical well-being?'
    ),
    (
        30,
        'Q2',
        'What practice involves integrating breath, movement, and meditation?'
    ),
    (
        30,
        'Q3',
        'What is the concept of cultivating awareness of ones own thoughts and feelings?'
    );
INSERT INTO Tag (title)
VALUES ('Maths'),
    ("Science"),
    ('Physics'),
    ('Biology'),
    ("Chemistry"),
    ("Business"),
    ("Admin"),
    ("Economics"),
    ("Computing Science"),
    ("Social Subjects"),
    ("History"),
    ("Modern Studies"),
    ("Geography"),
    ("English"),
    ("Languages"),
    ("French"),
    ("German"),
    ("Gaelic"),
    ("Latin"),
    ("Art"),
    ("Music"),
    ("Design and Technology"),
    ("Graphic Communication"),
    ("Engineering Science"),
    ("Design and Manufacture"),
    ("PE"),
    ("National 4"),
    ("National 5"),
    ("Higher"),
    ("Advanced Higher");
-- Assigning Tags to Decks
-- Science Facts deck tagged with 'Science'
INSERT INTO Deck_Topic (deck_id, tag_id)
VALUES (1, 1);
-- Vocabulary Builder deck tagged with 'Language'
INSERT INTO Deck_Topic (deck_id, tag_id)
VALUES (2, 2);
-- History Trivia deck tagged with 'History'
INSERT INTO Deck_Topic (deck_id, tag_id)
VALUES (3, 3);
-- Math Challenges deck tagged with 'Science'
INSERT INTO Deck_Topic (deck_id, tag_id)
VALUES (4, 1);
-- Nature and Wildlife deck tagged with 'Science'
INSERT INTO Deck_Topic (deck_id, tag_id)
VALUES (5, 1);
-- Word Puzzles deck tagged with 'Language'
INSERT INTO Deck_Topic (deck_id, tag_id)
VALUES (6, 2);
-- Geography Quiz deck tagged with 'Science'
INSERT INTO Deck_Topic (deck_id, tag_id)
VALUES (7, 1);
-- Art Appreciation deck tagged with 'Art'
INSERT INTO Deck_Topic (deck_id, tag_id)
VALUES (8, 5);
-- Literature Classics deck tagged with 'Language'
INSERT INTO Deck_Topic (deck_id, tag_id)
VALUES (9, 2);
-- Music History deck tagged with 'Art'
INSERT INTO Deck_Topic (deck_id, tag_id)
VALUES (10, 5);
-- Inserting User Likes
-- User 1 likes 'Science' and 'Health'
INSERT INTO User_Likes (user_id, tag_id)
VALUES (1, 1);
INSERT INTO User_Likes (user_id, tag_id)
VALUES (1, 4);
-- User 2 likes 'Language' and 'Art'
INSERT INTO User_Likes (user_id, tag_id)
VALUES (2, 2);
INSERT INTO User_Likes (user_id, tag_id)
VALUES (2, 5);
-- User 3 likes 'History' and 'Science'
INSERT INTO User_Likes (user_id, tag_id)
VALUES (3, 3);
INSERT INTO User_Likes (user_id, tag_id)
VALUES (3, 1);
-- User 4 likes 'Health' and 'Science'
INSERT INTO User_Likes (user_id, tag_id)
VALUES (4, 4);
INSERT INTO User_Likes (user_id, tag_id)
VALUES (4, 1);
-- User 5 likes 'Language' and 'Art'
INSERT INTO User_Likes (user_id, tag_id)
VALUES (5, 2);
INSERT INTO User_Likes (user_id, tag_id)
VALUES (5, 5);
-- User 6 likes 'History' and 'Art'
INSERT INTO User_Likes (user_id, tag_id)
VALUES (6, 3);
INSERT INTO User_Likes (user_id, tag_id)
VALUES (6, 5);
-- User 7 likes 'Language' and 'Science'
INSERT INTO User_Likes (user_id, tag_id)
VALUES (7, 2);
INSERT INTO User_Likes (user_id, tag_id)
VALUES (7, 1);
-- User 8 likes 'History' and 'Health'
INSERT INTO User_Likes (user_id, tag_id)
VALUES (8, 3);
INSERT INTO User_Likes (user_id, tag_id)
VALUES (8, 4);
-- User 9 likes 'Science' and 'Art'
INSERT INTO User_Likes (user_id, tag_id)
VALUES (9, 1);
INSERT INTO User_Likes (user_id, tag_id)
VALUES (9, 5);
-- User 10 likes 'Health' and 'Language'
INSERT INTO User_Likes (user_id, tag_id)
VALUES (10, 4);
INSERT INTO User_Likes (user_id, tag_id)
VALUES (10, 2);
-- Inserting User Saves
-- User 1 saves 'Science Facts' and 'Math Challenges' decks
INSERT INTO User_Save (user_id, deck_id)
VALUES (1, 1);
INSERT INTO User_Save (user_id, deck_id)
VALUES (1, 4);
-- User 2 saves 'Vocabulary Builder' and 'Word Puzzles' decks
INSERT INTO User_Save (user_id, deck_id)
VALUES (2, 2);
INSERT INTO User_Save (user_id, deck_id)
VALUES (2, 6);
-- User 3 saves 'History Trivia' and 'Geography Quiz' decks
INSERT INTO User_Save (user_id, deck_id)
VALUES (3, 3);
INSERT INTO User_Save (user_id, deck_id)
VALUES (3, 7);
-- User 4 saves 'Math Challenges' and 'Nature and Wildlife' decks
INSERT INTO User_Save (user_id, deck_id)
VALUES (4, 4);
INSERT INTO User_Save (user_id, deck_id)
VALUES (4, 5);
-- User 5 saves 'Word Puzzles' and 'Music History' decks
INSERT INTO User_Save (user_id, deck_id)
VALUES (5, 6);
INSERT INTO User_Save (user_id, deck_id)
VALUES (5, 10);
-- User 6 saves 'Art Appreciation' and 'Literature Classics' decks
INSERT INTO User_Save (user_id, deck_id)
VALUES (6, 8);
INSERT INTO User_Save (user_id, deck_id)
VALUES (6, 9);
-- User 7 saves 'Geography Quiz' and 'Music History' decks
INSERT INTO User_Save (user_id, deck_id)
VALUES (7, 7);
INSERT INTO User_Save (user_id, deck_id)
VALUES (7, 10);
-- User 8 saves 'Famous Quotes' and 'Mindful Meditation' decks
INSERT INTO User_Save (user_id, deck_id)
VALUES (8, 11);
INSERT INTO User_Save (user_id, deck_id)
VALUES (8, 14);
-- User 9 saves 'Astronomy Wonders' and 'Animal Kingdom' decks
INSERT INTO User_Save (user_id, deck_id)
VALUES (9, 12);
INSERT INTO User_Save (user_id, deck_id)
VALUES (9, 15);
-- User 10 saves 'Healthy Living Tips' and 'World Cuisine' decks
INSERT INTO User_Save (user_id, deck_id)
VALUES (10, 13);
INSERT INTO User_Save (user_id, deck_id)
VALUES (10, 16);
-- Inserting User Plays
-- User 1 plays 'Science Facts' with a score of 95
INSERT INTO User_Play (user_id, deck_id, score)
VALUES (1, 1, 95);
-- User 2 plays 'Vocabulary Builder' with a score of 80
INSERT INTO User_Play (user_id, deck_id, score)
VALUES (2, 2, 80);
-- User 3 plays 'History Trivia' with a score of 70
INSERT INTO User_Play (user_id, deck_id, score)
VALUES (3, 3, 70);
-- User 4 plays 'Math Challenges' with a score of 60
INSERT INTO User_Play (user_id, deck_id, score)
VALUES (4, 4, 60);
-- User 5 plays 'Word Puzzles' with a score of 90
INSERT INTO User_Play (user_id, deck_id, score)
VALUES (5, 6, 90);
-- User 6 plays 'Art Appreciation' with a score of 85
INSERT INTO User_Play (user_id, deck_id, score)
VALUES (6, 8, 85);
-- User 7 plays 'Geography Quiz' with a score of 75
INSERT INTO User_Play (user_id, deck_id, score)
VALUES (7, 7, 75);
-- User 8 plays 'Famous Quotes' with a score of 65
INSERT INTO User_Play (user_id, deck_id, score)
VALUES (8, 11, 65);
-- User 9 plays 'Astronomy Wonders' with a score of 70
INSERT INTO User_Play (user_id, deck_id, score)
VALUES (9, 12, 70);
-- User 10 plays 'Healthy Living Tips' with a score of 80
INSERT INTO User_Play (user_id, deck_id, score)
VALUES (10, 13, 80);
INSERT INTO User_Play (user_id, deck_id, score, timestamp)
VALUES (1, 1, 9, "2023-10-13 22:24:57"),
    (1, 1, 9, "2023-10-14 22:24:57"),
    (1, 1, 9, "2023-10-15 01:24:57"),
    (1, 1, 9, "2023-10-16 22:24:57"),
    (1, 1, 9, "2023-10-17 22:24:57"),
    (1, 1, 9, "2023-10-19 22:24:57"),
    (1, 1, 9, "2023-10-20 22:24:57"),
    (1, 1, 9, "2023-10-21 03:24:57"),
    (1, 1, 9, "2023-10-22 22:24:57");