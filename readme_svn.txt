
-------------------------------------------------------------------------------

               #####  ######  ####  #####  ##   ## ###### ##
               ##  ## ##     ##  ## ##  ## ### ### ##     ##
               #####  ####   ###### ##  ## ## # ## ####   ##
               ##  ## ##     ##  ## ##  ## ##   ## ##     
               ##  ## ###### ##  ## #####  ##   ## ###### ##

-------------------------------------------------------------------------------

OK, it seems you have successfully acquired the solidMatter sourcecode. I guess
i can assume that you might have write access to the repository, too. There are
some rules that you should follow to keep the repository uncluttered and
usable:

1. Don't submit unfinished work!
   Sometimes you might feel the urge to submit sources which are WIP just to
   have them  in a secure place. This can be OK, but only if your submit is
   not breaking the app and the important changes are done or in a stable,
   intermediate stage. Ideally, you have documented remaining issues with TODO:
   or FIXME: at least.

2. Don't spellcheck/reformat unchanged code!
   There's always something minor you find in the code or comments, a spelling
   error, some unnecessary spaces before a newline, some ugly formatting. Don't
   change these unless you're already doing changes to the code in that 
   section/file. Minor changes are irritating when you browse the history.

3. Commit changes to modules seperately!
   Currently the core code is stored in the same repository as the module's
   code. And most likely you will do changes in several modules at once. To 
   make keeping track of changes easier, you should commit the sources for each
   touched module seperately and name the module in the version description.

4. Don't add irrelevant stuff!
   There are files you need to work on the code, or that are generated while
   developing. Temp files, logs, IDE-specific files, .bak scripts when doing
   major changes, thumbs.db and the like. These should never be included in the
   commits.
   
5. Don't commit a playaround DB dump!
   The file solidmatter.sql in the /files folder contains the 'master' DB dump.
   As long as there's no install procedure for the core and the modules, this
   will be the place where all DB-specific changes are stored. If you add or
   modify entries (sbCR repository changes to nodetypes etc.), you have to dump
   the current state in this file before a commit. That means you should NOT 
   use this master DB for testing, i recommend that you use a seperate 
   installation (repository "test"), make sbCR repository changes in the master
   ("dev") and transport these to the testing DB. (Navicat is a nice tool to
   make this process easier)

There are exceptions to 1, 2 and 3, especially in the current state the code is
in, but try to stick to these rules, in the end it helps everyone working with
the repository...