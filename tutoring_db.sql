-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 19, 2025 at 06:32 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tutoring_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

CREATE TABLE `badges` (
  `id` int(11) NOT NULL,
  `badge_name` varchar(50) NOT NULL,
  `threshold` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `badges`
--

INSERT INTO `badges` (`id`, `badge_name`, `threshold`, `image_path`) VALUES
(1, 'Beginner Badge', 20, 'beginner_badge.png'),
(2, 'Intermediate Badge', 60, 'intermediate_badge.png'),
(3, 'Pro Badge', 80, 'pro_badge.png');

-- --------------------------------------------------------

--
-- Table structure for table `capstone_projects`
--

CREATE TABLE `capstone_projects` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `project_title` varchar(255) NOT NULL,
  `project_description` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `capstone_projects`
--

INSERT INTO `capstone_projects` (`id`, `student_id`, `project_title`, `project_description`, `submitted_at`) VALUES
(1, 1, 'Final Python Project', 'A project integrating course concepts.', '2025-03-05 14:28:45');

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`id`, `student_id`, `course_id`, `issued_at`) VALUES
(1, 1, 1, '2025-03-05 14:28:45');

-- --------------------------------------------------------

--
-- Table structure for table `chapters`
--

CREATE TABLE `chapters` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `chapter_name` varchar(255) NOT NULL,
  `chapter_description` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chapters`
--

INSERT INTO `chapters` (`id`, `module_id`, `chapter_name`, `chapter_description`, `sort_order`) VALUES
(1, 1, 'Chapter 1: Introduction to Python', 'Learn the basics and set up Python.', 1),
(2, 2, 'Chapter 1: Python Basics', 'Learn syntax, variables, and data types.', 1),
(3, 3, 'Chapter 1: Control Structures', 'Learn conditionals and loops.', 1),
(4, 4, 'Chapter 1: Functions', 'Learn to define and call functions.', 1),
(5, 5, 'Chapter 1: Data Structures', 'Learn about lists, tuples, and dictionaries.', 1),
(6, 6, 'Chapter 1: File I/O', 'Learn file operations.', 1),
(7, 7, 'Chapter 1: Exception Handling', 'Learn to handle exceptions.', 1),
(8, 8, 'Chapter 1: Object-Oriented Programming', 'Learn about classes and objects.', 1),
(9, 9, 'Chapter 1: Advanced Topics', 'Learn advanced Python topics.', 1),
(10, 10, 'Chapter 1: Capstone Project', 'Plan your final project.', 1),
(11, 11, 'Chapter 1: Getting Started with C++', 'Set up your C++ environment and write your first program.', 1),
(12, 12, 'Chapter 1: Basic Syntax and Data Types', 'Learn C++ syntax and basic data types.', 1),
(13, 13, 'Chapter 1: Control Structures', 'Practice conditionals and loops in C++.', 1),
(14, 14, 'Chapter 1: Arrays and Strings', 'Learn about arrays and C-style strings.', 1),
(15, 15, 'Chapter 1: Pointers and Memory Management', 'Introduction to pointers and dynamic memory allocation.', 1),
(16, 16, 'Chapter 1: Classes and Objects', 'Define classes and create objects in C++.', 1),
(17, 17, 'Chapter 1: Inheritance and Polymorphism', 'Understand inheritance and polymorphism in C++.', 1),
(18, 18, 'Chapter 1: Templates and STL', 'Learn about templates and using STL containers.', 1),
(19, 19, 'Chapter 1: Exception Handling and File I/O', 'Learn to handle exceptions and perform file I/O.', 1),
(20, 20, 'Chapter 1: Advanced C++ Topics', 'Explore modern C++ features and prepare for a capstone project.', 1),
(21, 21, 'Chapter 1: Introduction to Java', 'Overview of Java, history, and setup.', 1),
(22, 22, 'Chapter 1: Java Basics', 'Learn syntax, variables, and data types in Java.', 1),
(23, 23, 'Chapter 1: Control Structures in Java', 'Learn conditionals and loops in Java.', 1),
(24, 24, 'Chapter 1: Object-Oriented Programming in Java', 'Introduction to classes, objects, inheritance, and polymorphism in Java.', 1),
(25, 25, 'Chapter 1: Advanced Java Topics', 'Explore advanced topics like multithreading, collections, and lambda expressions in Java.', 1),
(26, 26, 'Chapter 1: Getting Started with Web Development', 'Overview of web development, tools, and environments.', 1),
(27, 27, 'Chapter 1: HTML & CSS Basics', 'Learn the basics of HTML structure and CSS styling.', 1),
(28, 28, 'Chapter 1: JavaScript Fundamentals', 'Learn core JavaScript concepts and DOM manipulation.', 1),
(29, 29, 'Chapter 1: Backend Development', 'Introduction to server-side programming and databases.', 1),
(30, 30, 'Chapter 1: Full Stack Integration', 'Combine frontend and backend skills to build complete web applications.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_name`, `description`) VALUES
(1, 'Python Course', 'A comprehensive Python course covering basics to advanced.'),
(2, 'C++ Course', 'Learn the fundamentals and advanced topics of C++.'),
(3, 'Java Course', 'Master Java programming from scratch.'),
(4, 'Web Development Course', 'Learn front-end and back-end web development.');

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `lesson_title` varchar(255) NOT NULL,
  `lecture_content` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id`, `chapter_id`, `lesson_title`, `lecture_content`, `video_url`, `sort_order`) VALUES
(1, 1, 'Lesson: Hello, World!', 'This lesson teaches you to write and run your first Python script.', 'https://www.youtube.com/shorts/nluUYtejoIE', 1),
(2, 2, 'Lesson: Variables', 'Learn how to declare and use variables in Python.', 'https://www.youtube.com/watch?v=7IoQ5BGkTJo', 1),
(3, 3, 'Lesson: Loops and Conditionals', 'Learn to use if statements and loops in Python.', 'https://www.youtube.com/watch?v=FvMPfrgGeKs', 1),
(4, 4, 'Lesson: Functions', 'Learn how to create and call functions.', 'https://www.youtube.com/watch?v=89cGQjB5R4M', 1),
(5, 5, 'Lesson: Lists and Tuples', 'Learn about lists and tuples.', 'https://www.youtube.com/watch?v=gOMW_n2-2Mw', 1),
(6, 6, 'Lesson: File Operations', 'Learn how to perform file I/O in Python.', 'https://www.youtube.com/watch?v=gOMW_n2-2Mw', 1),
(7, 7, 'Lesson: Exception Handling', 'Learn how to handle errors using try-catch.', 'https://www.youtube.com/watch?v=NIWwJbo-9_8', 1),
(8, 8, 'Lesson: Classes', 'Learn to create classes and objects in Python.', 'https://www.youtube.com/watch?v=ZDa-Z5JzLYM', 1),
(9, 9, 'Lesson: Advanced Python', 'Learn advanced topics like decorators and generators.', 'https://www.example.com/python-advanced.mp4', 1),
(10, 10, 'Lesson: Capstone Project', 'Plan your final Python project.', 'https://www.youtube.com/watch?v=qS4mvrWWO_M', 1),
(11, 11, 'Lesson: Hello, World! in C++', 'Write and run your first C++ program.', 'https://www.youtube.com/watch?v=VWJWUR-UnzQ', 1),
(12, 12, 'Lesson: Variables in C++', 'Learn how to declare and use variables in C++.', 'https://www.youtube.com/watch?v=vSTesJdgRCU', 1),
(13, 3, 'Lesson: If Statements and Loops', 'Understand conditionals and loops in C++.', 'https://www.youtube.com/watch?v=kfZEZj1IOBE', 1),
(14, 14, 'Lesson: Working with Arrays', 'Learn array declaration and iteration.', 'https://www.youtube.com/watch?v=eE9MnoS0lc0', 1),
(15, 15, 'Lesson: Pointers in C++', 'Learn pointer basics and memory allocation.', 'https://www.youtube.com/watch?v=slzcWKWCMBg', 1),
(16, 16, 'Lesson: Defining Classes', 'Learn how to define and use classes in C++.', 'https://www.youtube.com/watch?v=W1CjYKmTB-c', 1),
(17, 17, 'Lesson: Inheritance Basics', 'Understand inheritance and virtual functions in C++.', 'https://www.youtube.com/watch?v=rJlJ8qqVm3k', 1),
(18, 18, 'Lesson: Introduction to Templates', 'Learn about templates and basic STL usage.', 'https://www.youtube.com/watch?v=mQqzP9EWu58', 1),
(19, 19, 'Lesson: Exception Handling', 'Learn to handle exceptions in C++.', 'https://www.youtube.com/watch?v=5nCXSDv6e4I', 1),
(20, 20, 'Lesson: Modern C++ Features', 'Explore lambda expressions, smart pointers, and multithreading.', 'https://www.youtube.com/watch?v=UOB7-B2MfwA', 1),
(21, 21, 'Lesson: Installing Java and Setting Up IDE', 'Step-by-step guide to installing Java and setting up an IDE.', 'https://www.youtube.com/watch?v=jPwrWjEwtrw', 1),
(22, 22, 'Lesson: Basic Syntax in Java', 'Introduction to Java syntax, variables, and data types.', 'https://www.youtube.com/watch?v=so1iUWaLmKA&t=11s', 1),
(23, 23, 'Lesson: Control Structures in Java', 'Learn if-else statements, loops, and switch cases in Java.', 'https://www.youtube.com/watch?v=MY03bt_0JQI', 1),
(24, 24, 'Lesson: Object-Oriented Programming in Java', 'Understand classes, objects, inheritance, and polymorphism in Java.', 'https://www.youtube.com/watch?v=jhDUxynEQRI', 1),
(25, 25, 'Lesson: Advanced Java Features', 'Explore multithreading, collections, and lambda expressions in Java.', 'https://www.youtube.com/watch?v=tj5sLSFjVj4&t=17s', 1),
(26, 26, 'Lesson: Introduction to Web Development', 'Overview of web development and setting up your environment.', 'https://www.youtube.com/watch?v=PlxWf493en4', 1),
(27, 27, 'Lesson: HTML Basics', 'Learn the structure of a webpage using HTML.', 'https://www.youtube.com/watch?v=i1FeOOhNnwU', 1),
(28, 28, 'Lesson: CSS Styling', 'Learn to style your webpage using CSS.', 'https://www.youtube.com/watch?v=lkIFF4maKMU', 1),
(29, 29, 'Lesson: JavaScript Essentials', 'Introduction to JavaScript and its core concepts.', 'https://www.youtube.com/watch?v=GxmfcnU3feo&t=135s', 1),
(30, 30, 'Lesson: Building a Full-Stack Application', 'Combine frontend and backend skills to create a complete web application.', 'https://www.youtube.com/watch?v=dPMk6_HTBq8', 1);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `module_name` varchar(255) NOT NULL,
  `module_description` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `course_id`, `module_name`, `module_description`, `sort_order`) VALUES
(1, 1, 'Module 1: Introduction and Setup', 'Introduction to Python and environment setup.', 1),
(2, 1, 'Module 2: Basics', 'Learn basic Python syntax, variables, and data types.', 2),
(3, 1, 'Module 3: Control Structures', 'Learn conditionals and loops.', 3),
(4, 1, 'Module 4: Functions', 'Learn to write and use functions.', 4),
(5, 1, 'Module 5: Data Structures', 'Learn about lists, tuples, and dictionaries.', 5),
(6, 1, 'Module 6: File I/O', 'Learn to read from and write to files.', 6),
(7, 1, 'Module 7: Exception Handling', 'Learn how to handle exceptions.', 7),
(8, 1, 'Module 8: OOP', 'Introduction to classes and objects.', 8),
(9, 1, 'Module 9: Advanced Topics', 'Learn advanced Python topics.', 9),
(10, 1, 'Module 10: Capstone Project', 'Final project integrating all topics.', 10),
(11, 2, 'Module 1: Introduction & Setup', 'Introduction to C++ and environment setup.', 1),
(12, 2, 'Module 2: Basic Syntax & Data Types', 'Learn basic C++ syntax, variables, and data types.', 2),
(13, 2, 'Module 3: Control Structures', 'Learn conditionals and loops in C++.', 3),
(14, 2, 'Module 4: Functions', 'Learn to define and call functions in C++.', 4),
(15, 2, 'Module 5: Arrays & Strings', 'Learn about arrays and C-style strings.', 5),
(16, 2, 'Module 6: Pointers & Memory', 'Learn about pointers and dynamic memory allocation.', 6),
(17, 2, 'Module 7: Object-Oriented Programming', 'Introduction to classes and objects in C++.', 7),
(18, 2, 'Module 8: Advanced OOP', 'Learn inheritance, polymorphism, and virtual functions.', 8),
(19, 2, 'Module 9: Templates & STL', 'Learn about templates and the STL.', 9),
(20, 2, 'Module 10: Advanced Topics', 'Modern C++ features and project-based applications.', 10),
(21, 3, 'Module 1: Introduction and Setup', 'Introduction to Java and environment setup.', 1),
(22, 3, 'Module 2: Java Basics', 'Learn basic Java syntax, variables, and data types.', 2),
(23, 3, 'Module 3: Control Structures', 'Learn conditionals and loops in Java.', 3),
(24, 3, 'Module 4: Object-Oriented Programming', 'Learn classes, objects, inheritance, and polymorphism in Java.', 4),
(25, 3, 'Module 5: Advanced Topics', 'Advanced topics including multithreading and collections.', 5),
(26, 4, 'Module 1: Introduction to Web Development', 'Introduction to web development, tools and environment setup.', 1),
(27, 4, 'Module 2: HTML & CSS Basics', 'Learn the fundamentals of HTML and CSS for building websites.', 2),
(28, 4, 'Module 3: JavaScript Fundamentals', 'Learn basic JavaScript, DOM manipulation, and events.', 3),
(29, 4, 'Module 4: Backend Development', 'Introduction to backend development using a server-side language.', 4),
(30, 4, 'Module 5: Full Stack Integration', 'Integrate frontend and backend to build a full-stack application.', 5);

-- --------------------------------------------------------

--
-- Table structure for table `peer_posts`
--

CREATE TABLE `peer_posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peer_posts`
--

INSERT INTO `peer_posts` (`id`, `user_id`, `content`, `created_at`) VALUES
(1, 1, 'Great course!', '2025-03-05 14:28:45');

-- --------------------------------------------------------

--
-- Table structure for table `peer_reviews`
--

CREATE TABLE `peer_reviews` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `review_text` text NOT NULL,
  `rating` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peer_reviews`
--

INSERT INTO `peer_reviews` (`id`, `student_id`, `reviewer_id`, `review_text`, `rating`) VALUES
(1, 1, 2, 'Very helpful!', 5);

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `quiz_title` varchar(255) NOT NULL,
  `quiz_content` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `chapter_id`, `quiz_title`, `quiz_content`, `sort_order`) VALUES
(1, 11, 'Quiz: C++ Introduction', 'Test your knowledge on setting up C++ and writing your first program.', 1),
(2, 12, 'Quiz: C++ Basics', 'Test your understanding of variables and basic types in C++.', 1),
(3, 3, 'Quiz: Control Structures', 'Test your knowledge on loops and conditionals in C++.', 1),
(4, 14, 'Quiz: Arrays and Strings', 'Test your understanding of arrays and C-style strings in C++.', 1),
(5, 15, 'Quiz: Pointers', 'Test your grasp on pointers and dynamic memory allocation.', 1),
(6, 16, 'Quiz: OOP in C++', 'Test your knowledge on classes and objects.', 1),
(7, 17, 'Quiz: Inheritance', 'Test your understanding of inheritance and polymorphism.', 1),
(8, 18, 'Quiz: Templates and STL', 'Test your knowledge on templates and using the STL.', 1),
(9, 19, 'Quiz: Exception Handling', 'Test your knowledge on error handling in C++.', 1),
(10, 20, 'Quiz: Advanced C++', 'Test your understanding of modern C++ features.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `option_a` varchar(255) DEFAULT NULL,
  `option_b` varchar(255) DEFAULT NULL,
  `option_c` varchar(255) DEFAULT NULL,
  `option_d` varchar(255) DEFAULT NULL,
  `correct_option` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `quiz_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`) VALUES
(1, 1, 'What is the correct file extension for a C++ source file?', '.cpp', '.cxx', '.cc', 'All of the above', 'D'),
(2, 1, 'Which of the following is the correct syntax for the main function in C++?', 'int main()', 'void main()', 'main()', 'public static void main()', 'A'),
(3, 1, 'Which of the following is required to compile and run a C++ program?', 'An interpreter', 'A web browser', 'A compiler', 'A text editor', 'C'),
(4, 1, 'Which command is used to compile a C++ program using the g++ compiler in Linux?', 'g++ program.cpp -o program', 'compile program.cpp', 'run program.cpp', 'cpp program.cpp', 'A'),
(5, 1, 'What will be the output of the following C++ program?\n\n#include <iostream>\nusing namespace std;\nint main() {\n    cout << \"Hello, World!\";\n    return 0;\n}', 'Hello, World!', 'Hello, World! (on a new line)', 'Compilation error', 'Runtime error', 'A'),
(6, 1, 'What is the purpose of #include <iostream> in a C++ program?', 'To include the main function', 'To enable input and output operations', 'To define a class', 'To declare variables', 'B'),
(7, 1, 'What does using namespace std; do in a C++ program?', 'It allows the use of standard library functions without prefixing them with std::', 'It defines a new namespace', 'It is required in every C++ program', 'It has no effect', 'A'),
(8, 1, 'Which statement is used to take input from the user in C++?', 'cin >>', 'cout <<', 'scanf()', 'input()', 'A'),
(9, 1, 'What is the return type of the main() function in C++?', 'void', 'int', 'char', 'float', 'B'),
(10, 1, 'What happens if the return 0; statement is missing in the main() function?', 'The program will not compile', 'It will result in a runtime error', 'It may still work, as returning 0 is optional in newer compilers', 'The program will always crash', 'C'),
(11, 2, 'Which of the following is a valid way to declare an integer variable in C++?', 'int x;', 'integer x;', 'var x;', 'num x;', 'A'),
(12, 2, 'What is the size of an int in C++ (assuming a 32-bit system)?', '2 bytes', '4 bytes', '8 bytes', 'System dependent', 'B'),
(13, 2, 'What is the correct syntax to declare a constant variable in C++?', 'constant int x = 10;', '#define x 10', 'const int x = 10;', 'var x = 10;', 'C'),
(14, 2, 'Which of the following is the correct way to initialize a float variable?', 'float x = 10.5;', 'float x = 10;', 'float x = 10.5f;', 'All of the above', 'D'),
(15, 2, 'What is the range of values that a char variable can store?', '-128 to 127', '0 to 255', '0 to 127', '-256 to 255', 'A'),
(16, 2, 'Which data type should be used to store a large integer value (greater than int limits)?', 'short', 'float', 'long long', 'double', 'C'),
(17, 2, 'What will be the output of the following C++ code?\n\n#include <iostream>\nusing namespace std;\nint main() {\n    int a = 5;\n    float b = 2.5;\n    cout << a + b;\n    return 0;\n}', '7', '7.5', '5.25', 'Compilation error', 'B'),
(18, 2, 'Which of the following correctly declares a boolean variable in C++?', 'bool flag = true;', 'boolean flag = 1;', 'int flag = True;', 'var flag = true;', 'A'),
(19, 2, 'What happens if a variable is declared but not initialized?', 'It holds a default value of 0', 'It holds a garbage (random) value', 'The program will not compile', 'The program will crash', 'B'),
(20, 2, 'Which of the following correctly represents a double-precision floating-point number in C++?', 'double x = 10.5;', 'float x = 10.5;', 'long double x = 10.5;', 'Both a and c', 'D'),
(21, 3, 'Which of the following is the correct syntax for an if statement in C++?', 'if condition then {}', 'if (condition) {}', 'if {condition} then {}', 'if condition: {}', 'B'),
(22, 3, 'What is the output of the following code?\n\nint x = 10;\nif (x > 5)\n    cout << \"Greater\";\nelse\n    cout << \"Smaller\";', 'Greater', 'Smaller', 'Compilation Error', 'No output', 'A'),
(23, 3, 'How many times will the loop run?\n\nfor (int i = 0; i < 5; i++) { cout << i; }', '4 times', '5 times', '6 times', 'Infinite loop', 'B'),
(24, 3, 'What is the purpose of the break statement in loops?', 'To skip the current iteration', 'To exit the loop immediately', 'To repeat the loop', 'To end the program', 'B'),
(25, 3, 'What will be the output of this while loop?\n\nint i = 0;\nwhile (i < 3) {\n    cout << i;\n    i++;\n}', '012', '123', '01', 'Infinite loop', 'A'),
(26, 3, 'What is the correct way to implement a do-while loop?', 'do while (condition) {}', 'do { } while (condition);', 'while (condition) do {}', 'repeat { } until (condition);', 'B'),
(27, 3, 'What will the following code print?\n\nfor (int i = 1; i <= 5; i++) {\n    if (i == 3) break;\n    cout << i;\n}', '12345', '12', '345', '15', 'B'),
(28, 3, 'What is the use of the continue statement in loops?', 'To stop the loop execution', 'To skip the current iteration and move to the next', 'To exit the loop', 'To pause the loop', 'B'),
(29, 3, 'What will be the output of the following nested loop?\n\nfor (int i = 1; i <= 2; i++) {\n    for (int j = 1; j <= 2; j++) {\n        cout << i << j << \" \";\n    }\n}', '11 12 21 22', '12 21', '1122', '2211', 'A'),
(30, 3, 'What will happen if the condition in a while loop is always true?', 'The loop will execute a fixed number of times', 'The program will terminate', 'The loop will run indefinitely (infinite loop)', 'The loop will not run at all', 'C'),
(31, 4, 'How do you declare an array of 5 integers in C++?', 'int arr(5);', 'int arr[5];', 'array<int, 5> arr;', 'int arr = new int[5];', 'B'),
(32, 4, 'What is the index of the first element in a C++ array?', '0', '1', '-1', 'It depends on the compiler', 'A'),
(33, 4, 'What happens if you access an array index out of its bounds in C++?', 'It throws an error', 'It results in undefined behavior', 'It resets to the first index', 'The compiler automatically fixes it', 'B'),
(34, 4, 'Which of the following correctly initializes a C-style string?', 'char str[] = \"Hello\";', 'char str[6] = \"Hello\";', 'char str[] = {\'H\', \'e\', \'l\', \'l\', \'o\', \'\\0\'};', 'All of the above', 'D'),
(35, 4, 'How do you properly access the last element of an array named arr with n elements?', 'arr[n]', 'arr[n-1]', 'arr[n+1]', 'arr[-1]', 'B'),
(36, 4, 'What is the default value of an uninitialized array of integers in C++?', '0', '-1', 'Garbage values', 'Compiler error', 'C'),
(37, 4, 'Which function is used to copy one C-style string to another?', 'strcpy()', 'strcat()', 'strcopy()', 'copystr()', 'A'),
(38, 4, 'How do you find the length of a C-style string?', 'strlen(str)', 'length(str)', 'str.len()', 'str.size()', 'A'),
(39, 4, 'What happens if you assign one C-style string to another using =?', 'The contents are copied', 'It causes a compilation error', 'A pointer to the string is assigned', 'The string is appended', 'B'),
(40, 4, 'What is the last character of every C-style string?', '\'\\0\'', '\'\\n\'', '\' \'', '\'.\'', 'A'),
(41, 5, 'What is a pointer in C++?', 'A variable that stores memory addresses', 'A function that returns memory', 'A data type used to store integers', 'A keyword in C++', 'A'),
(42, 5, 'What is the correct syntax to declare a pointer to an integer?', 'int* ptr;', 'int ptr*;', 'int ptr;', 'pointer ptr;', 'A'),
(43, 5, 'Which operator is used to access the value stored at the memory address a pointer holds?', '&', '*', '->', '[]', 'B'),
(44, 5, 'How do you allocate memory dynamically for an integer using pointers?', 'int *ptr = new int;', 'int ptr = malloc(sizeof(int));', 'int ptr = new int;', 'allocate(ptr, int);', 'A'),
(45, 5, 'How do you properly free dynamically allocated memory in C++?', 'free(ptr);', 'delete ptr;', 'remove(ptr);', 'clear(ptr);', 'B'),
(46, 5, 'What happens if you access a pointer that has been deleted?', 'It throws a runtime error', 'It results in undefined behavior', 'It resets to null', 'The compiler automatically fixes it', 'B'),
(47, 5, 'What is the difference between new and new[] in C++?', 'new allocates a single variable, new[] allocates an array', 'They are identical', 'new is for objects, new[] is for integers', 'new works only with classes', 'A'),
(48, 5, 'What happens if you forget to use delete on dynamically allocated memory?', 'The memory will be deallocated automatically', 'The program will terminate', 'A memory leak will occur', 'Compilation error', 'C'),
(49, 5, 'How do you correctly deallocate an array allocated with new[]?', 'delete ptr;', 'delete[] ptr;', 'free(ptr);', 'remove(ptr);', 'B'),
(50, 5, 'What is a null pointer?', 'A pointer that stores an invalid address', 'A pointer that stores the value zero', 'A pointer that points to no valid memory location', 'A pointer that stores an integer', 'C'),
(51, 6, 'What is a class in C++?', 'A blueprint for creating objects', 'A function in C++', 'A keyword for defining variables', 'A special type of pointer', 'A'),
(52, 6, 'What is an object in C++?', 'A variable of class type', 'A function that belongs to a class', 'A pointer to a class', 'A keyword used for inheritance', 'A'),
(53, 6, 'How do you declare a class in C++?', 'class ClassName { };', 'object ClassName { };', 'struct ClassName { };', 'declare ClassName { };', 'A'),
(54, 6, 'What is the default access modifier for class members in C++?', 'public', 'private', 'protected', 'internal', 'B'),
(55, 6, 'Which keyword is used to create an object of a class in C++?', 'object', 'class', 'new', 'No keyword is required', 'D'),
(56, 6, 'What is the purpose of a constructor in C++?', 'To initialize an object when it is created', 'To destroy an object when it is no longer needed', 'To create copies of an object', 'To declare private members', 'A'),
(57, 6, 'How do you define a destructor in C++?', 'By using ~ClassName()', 'By using delete ClassName()', 'By using destroy ClassName()', 'By using ClassName::~()', 'A'),
(58, 6, 'What is function overloading in C++?', 'Defining multiple functions with the same name but different parameters', 'Calling multiple functions simultaneously', 'Using multiple return types in a single function', 'Using the same function in different classes', 'A'),
(59, 6, 'What is the purpose of the this pointer in C++?', 'It refers to the current object of a class', 'It creates a copy of an object', 'It is used to call a function of the same class', 'It refers to the base class in inheritance', 'A'),
(60, 6, 'What is inheritance in C++?', 'A process of deriving a new class from an existing class', 'A mechanism to create multiple objects', 'A way to define private variables', 'A technique for overloading functions', 'A'),
(61, 7, 'What is inheritance in C++?', 'A mechanism to create multiple objects', 'A process of deriving a new class from an existing class', 'A way to define private variables', 'A method for memory management', 'B'),
(62, 7, 'Which keyword is used to inherit a class in C++?', 'extends', 'inherits', 'derived', ': (colon)', 'D'),
(63, 7, 'What is the base class in inheritance?', 'The class that is inherited from', 'The class that inherits another class', 'A class without any methods', 'A class with only private members', 'A'),
(64, 7, 'What is a derived class?', 'A class that is inherited from another class', 'A class that cannot be instantiated', 'A class without a constructor', 'A class that is inherited by another class', 'A'),
(65, 7, 'What type of inheritance allows a class to inherit from multiple classes?', 'Single inheritance', 'Multiple inheritance', 'Multilevel inheritance', 'Hierarchical inheritance', 'B'),
(66, 7, 'What is the purpose of the virtual keyword in C++?', 'To define a virtual base class', 'To enable dynamic method dispatch in polymorphism', 'To create multiple objects', 'To prevent inheritance', 'B'),
(67, 7, 'What is function overriding in C++?', 'Defining multiple functions with the same name in the same class', 'Redefining a base class function in the derived class', 'Using multiple functions in different classes', 'A method of defining variables inside a function', 'B'),
(68, 7, 'What is an abstract class in C++?', 'A class that cannot be inherited', 'A class that cannot be instantiated and contains at least one pure virtual function', 'A class with only static methods', 'A class with only private members', 'B'),
(69, 7, 'What is the correct syntax for defining a pure virtual function?', 'virtual void functionName() = 0;', 'void functionName() virtual;', 'abstract void functionName();', 'virtual functionName();', 'A'),
(70, 7, 'What is the key benefit of polymorphism in C++?', 'It allows multiple functions to run at the same time', 'It enables dynamic method dispatch and code reusability', 'It prevents objects from being created', 'It restricts inheritance between classes', 'B');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_results`
--

CREATE TABLE `quiz_results` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `taken_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_progress`
--

CREATE TABLE `student_progress` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `completed` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_progress`
--

INSERT INTO `student_progress` (`id`, `student_id`, `course_id`, `completed`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 0),
(3, 1, 3, 0),
(5, 3, 2, 30),
(6, 3, 1, 0),
(7, 3, 3, 0),
(8, 3, 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`) VALUES
(1, 'student@example.com', 'hashedpassword'),
(2, 'tutor@example.com', 'hashedpassword'),
(3, 'suyuach@gmail.com', '$2y$10$2S.GUuBoWpq9Qn2ztsCvquuVfsMJxOQ5wWWwvDFMT2Q7Xd7ZzSccu');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `capstone_projects`
--
ALTER TABLE `capstone_projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `chapters`
--
ALTER TABLE `chapters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_name` (`course_name`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chapter_id` (`chapter_id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `peer_posts`
--
ALTER TABLE `peer_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `peer_reviews`
--
ALTER TABLE `peer_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `reviewer_id` (`reviewer_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chapter_id` (`chapter_id`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `student_progress`
--
ALTER TABLE `student_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `badges`
--
ALTER TABLE `badges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `capstone_projects`
--
ALTER TABLE `capstone_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `chapters`
--
ALTER TABLE `chapters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `peer_posts`
--
ALTER TABLE `peer_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `peer_reviews`
--
ALTER TABLE `peer_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `quiz_results`
--
ALTER TABLE `quiz_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_progress`
--
ALTER TABLE `student_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `capstone_projects`
--
ALTER TABLE `capstone_projects`
  ADD CONSTRAINT `capstone_projects_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chapters`
--
ALTER TABLE `chapters`
  ADD CONSTRAINT `chapters_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `modules_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `peer_posts`
--
ALTER TABLE `peer_posts`
  ADD CONSTRAINT `peer_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `peer_reviews`
--
ALTER TABLE `peer_reviews`
  ADD CONSTRAINT `peer_reviews_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `peer_reviews_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD CONSTRAINT `quiz_results_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_results_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_progress`
--
ALTER TABLE `student_progress`
  ADD CONSTRAINT `student_progress_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_progress_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
