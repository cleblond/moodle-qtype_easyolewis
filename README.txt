  Moodle 2.3 plugin: EasyOChem Marvinsketch Lewis Structure and Charge question type

  by Carl LeBlond


INSTALLATION:

This will NOT work with Moodle 2.0 or older, since it uses the new
question API implemented in Moodle 2.1.

This is a Moodle question type. It should come as a self-contained 
"easyomech" folder which should be placed inside the "question/type" folder
which already exists on your Moodle web server.

Once you have done that, visit your Moodle admin page - the database 
tables should automatically be upgraded to include an extra table for
the EasyOChem Mechanism question type.

You must download a recent copy of Marvinsketch from www.chemaxon.com 
(free for academic use) and intall it in folder named "marvin" at your 
web root.  Alternatively you could edit the php scripts if your marvin 
installation is elsewhere.  This version of easyomech was developed 
using Marvinsketch 5.10.3_b102  


USAGE:

The EasyOChem Lewis structure/Formal Charge question can be used to design problems 
in which the students must provide electrons (radicals or pairs) or charges on atoms 
in molecules.  You can ask questions such as;

* Please provide all lone pair electron where required?"

* Indicate the formal charge on any atoms requiring it?

The student then choose the appropriate tool and adds the charges or electrons.
