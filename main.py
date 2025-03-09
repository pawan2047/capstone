import random

# Taking inputs for the length and width of the grid
l = random.randint(4, 15)  # Random length of grid
w = random.randint(4, 15)  # Random width of grid

print(f"Grid Size: Length = {l}, Width = {w}")

# Generate a random number between 1 and l for the x position s1 (row)
# Generate a random number between 1 and w for the y position s2 (column)
s1 = random.randint(1, l)  
s2 = random.randint(1, w)  

# Initializing the number of attempts
attempts = 0
guessed = False

# Guess the row position
print("\nTry to guess the row position of the treasure!")
while not guessed:
    row_guess = int(input("Enter your guess for the row position: "))
    attempts += 1

    if row_guess < s1:
        print(f"You guessed too low for the row position, try again! {attempts} attempts used.")
    elif row_guess > s1:
        print(f"You guessed too high for the row position, try again! {attempts} attempts used.")
    else:
        print(f"You guessed correctly! {attempts} attempts used. Now, try to guess the column position!")
        guessed = True

# Reset guessed flag for column guessing
guessed = False

# Guess the column position
while not guessed:
    col_guess = int(input("Enter your guess for the column position: "))
    attempts += 1

    if col_guess < s2:
        print(f"You guessed too low for the column position, try again! {attempts} attempts used.")
    elif col_guess > s2:
        print(f"You guessed too high for the column position, try again! {attempts} attempts used.")
    else:
        print(f"Congratulations! You guessed the position of the treasure correctly! {attempts} attempts used.")
        guessed = True

        #link to source code : https://github.com/pawan2047/main.py/blob/e86f9a547e95476efd7f94333a4c19c868e99409/main.py
