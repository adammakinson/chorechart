The goal of this branch is to separate out the management of chores (crud) from
the chore completion/review process for admins so that they have a dedicated view
for "their" chores and a dedicated view for managing chores.

In addition, I want to separate out the act of creating a chore and assigning
a chore to an individual so that they're not coupled together tightly.

Part of the goal here is to eliminate having to create a new same-named chore
everytime you want to assign one to a user. An admin should only need to create
a new chore IF one doesn't exist. They should be able to have free reign to
manage the chores list. Since the assingment of a chore essentially creates a
new chore instance (i.e. user-chore), the admin should be able to delete a chore
from the list without affecting the assinged chore history etc.

The way things work right now, there is a tie between chores and user-chores
via chore id which would need to be addressed since we want free reign to
modify chores without messing with user-chore instances (chores assigned to users)
OR transactions (record of chores completed or point spent). I should be able to
just add "chore" and "pointvalue" to user-chores and could potentially remove
the tie between chores and userchores (chore_id).

Ultimately, there is opportunity for more complex options when it comes to 
chore assignment. For example, a user might want to assign the same chore to
multiple individuals with the intent that those users each get an instance
of the chore. Alternatively, they may want to assign the chore to a group of
users with the intent that the chore is a shared effort.

The "team" chores scenario can be handled by creation of groups and the chore
can be assigned to the group.

How would the UX of this work?
    checkboxes for assignment so that there is a multi/multi scenario where
    multiple chores can be assigned to an individual and/or mulitple individuals
    can be assigned a chore. So, there would be two columns; a chores column and
    an "asignees" column that could contain individuals or groups. The user would
    check 1-N chores and 1-N "asignees"

    Hrm... perhaps the second column would need to be a tabbed thing to toggle
    between individuals and groups. We wouldn't want mixing of the two because
    of the complexity it would add (i.e. the potential to assign a chore to both
    a group and individuals that belong in that group and how to handle points and
    transactions etc.), so I think it makes sense to keep it either/or.

    Does it make sense as an admin to have an assignments tab that lists all users
    and their assigned chores with statuses etc.? I think so...

    So, overall, the manage chores view would have two tabs; one for chore creation and assignment
    and one for "assignments" (i.e. a listing of all users/groups/assigned chores).

    withing the "chore creation and assignment" tab, you would have two columns;
    one for chore management (crud) that would also have checkboxes for multi-assignment
    the other column would be a two-tabbed column that would allow choosing either
    groups or individuals to assign the chores to (along with an "assign" button
    to facilitate the assignments).

Sub-tasks for this task
    create the new view (rough draft mock up)
    
    add chore and pointvalue to user-chores table (create migration for this)

    potentially remove the direct tie between chores and user-chores

    create a new "chore management" view for admins.
    I may be able to modify the current view for my purposes
        remove the "checkmark" and associated actions
        remove the user dropdown from the "add chore"
        creation of 

    From the current "chores" view, remove the abilities to edit and delete
    chores from the admins list leaving only the checkmark action.