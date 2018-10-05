Website created as part of CSE 586 - Distributed Systems course. 
This website takes the start and destination as inputs from users and then displays the route on a map along with the weather information of all the cities en-route.
The Master branch has the client and server side PHP code for this implementation. 

The SQL-Implementation branch consists of MySQL implementation code that takes care of caching of queries using a MySQL database.

To run these files you need to install the LAMPP or XAMPP stack. I used LAMPP stack but MAMPP, XAMPP or WAMPP may also be used. Just make sure to use Apache Server and MySQL.
I used MariaDB for MySQL. 

For running the site on localhost, start the Apache server and MySQL database, and go to localhost/WeaPred/client.php
Enter the start and destination locations and hit "Submit". 
We can go back to the client page from the server page using the "Go Back" button. 
