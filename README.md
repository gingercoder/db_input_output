db_input_output
===============

MySQL manipulation class including import / export routines without need for shell exec commands


The class provides all of the database connection and manipulation routines for MySQL as most 
abstract MySQL database classes do. 
The import and export routines provide the ability to take a proper data cut and data import 
to your system without the need to do shell exec or mysql dump commands.

The routines can be stripped from this and used in your own applications, substituting the 
database calls for your own routines.

After being frustrated by not being able to undertake this funtionality without shell exec 
I decided to construct the routines.

Hopefully it's of some use to you all.
Rick