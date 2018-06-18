
# Git Rules

OK, it seems you have successfully acquired the solidMatter sourcecode. There are some rules that you should follow to keep the repository uncluttered and usable, although the switch to Git and Git Flow is rather recent, so there may be changes...

The version control follows the Git Flow method, which means that you should usually only branch off the "develop" branch for your fixes and features. Feature branches should be named "`<Module>_<Feature>`", e.g. "`sbSystem_Installer`" or "`sbJukebox_SortablePlaylist`". If possible these should be constrained to a specific module, but if necessary the changes may span over several modules (e.g. in a case where a central change to sbSystem requires changes to other modules, the topic of the change has a higher relevance than the affected files/folders).

It is not necessary to create a branch to implement fixes that you encounter while implementing features, as long as they are specified in the commit message (and only if relevant to other branches). If it is a rather large bugfix with several commits that you want to separate from other changes, use a feature branch with a Prefix "`Fix_`" for the feature, e.g. "`sbSystem_Fix_Nodeselector`".

Now for the general rules regarding commits:

## 1. Only push finished work to origin! (and only merge finished features to develop)
Sometimes you might feel the urge to push sources which are WIP just to have them  in a secure place. This can be OK, but only if it is not breaking the app and the important changes are done or in a stable, intermediate stage. Ideally, you have documented remaining issues with TODO: or FIXME: at least.

## 2. Don't spellcheck/reformat unchanged code!
There's always something minor you find in the code or comments, a spelling error, some unnecessary spaces before a newline, some ugly formatting. Don't change these unless you're already doing changes to the code in that section/file. Minor changes are irritating when you browse the history.

## 3. Don't add irrelevant stuff!
There are files you need to work on the code, or that are generated while developing. Temp files, logs, IDE-specific files, .bak scripts when doing major changes, thumbs.db and the like. These should never be included in the commits.
   
## 4. Don't commit a playaround DB dump!
The file solidmatter.sql in the /files folder contains the 'master' DB dump. As long as there's no install procedure for the core and the modules, this will be the place where all DB-specific changes are stored. If you add or modify entries (sbCR repository changes to nodetypes etc.), you have to dump the current state in this file before a commit. That means you should NOT use this master DB for testing, i recommend that you use a seperate installation (repository "test"), make sbCR repository changes in the master ("dev") and transport these to the testing DB.
Note: this is only a workaround and subject to change when module installers are implemented.

## 5. Commit messages...
A commit message should have one Line with the topic of the commit, and then several lines preceded by "- " for all individual changes that are worth mentioning. If there are dependant changes to other modules, these should be named in separate Blocks. See the following Example:

> sbSystem: new Inputfield type for drag'n'drop file uploads
- added class sbInput_dndupload
- changed sbForm to accept the new input type
- added the new type to _global/sbform.xsl
>
> sbFiles:
- changed the default input for uploads to the new input type
   
There are exceptions to all of these, especially in the current state the code is in and considering the git learning curve. But try to stick to these rules, in the end it helps everyone working with the repository...