# BestReads
Displays the bestsellers list and rank 1's cover pic with their title and author according to book genre.

## Summary
- Implements a Webpage which accepts book categories such as "Travel", "Hardcover Fiction", "Young And Adult" etc. and displays the bestsellers list (current) in a table with attributes: 'rank', 'title', 'author', 'description'.
- By the implementation of New_York_Times and Google_Books APIs.
- Language uses

    Frontend :- HTML and CSS

    Backend :- PHP

## Features
- Displays the bestsellers' list (in a table having at-most 10 values) according to book genre.
- Navigation for various pages of selected bestsellers' list is also provided.
- The book list is sorted in a descending order, according to #Rank.
- Displays all the genres sorted in ascending order for selection.
- Displays the rank 1's cover pic from the selected genre with their title and author names.

## Description

Parsing data from both APIs was not easy as sometime no data was available and many times they were not well-structured.

So, Various checks have been implemented to provide error free browsing experience.

Problems such as :-

- No ISBN available (Handled in line no. 174)
- ISBN available but book cover is not available. (Handled in line no. 184)
- Errors in "Total_Number_Of_Books" in provided data. (Handled in line no 164)
- No description found for some books, handled by showing message "No Description Found" (Handled in line no 405)

**you can see formatted json data fetched from above used APIs by uncommenting line no 180 or 181**


