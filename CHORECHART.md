Notes:

1.  User and permission overview
    a.  There are two kinds of users; general and admins.
    b.  general users can see chores assigned to them and can
        click a checkbox indicating their assigned task is ready
        for inspection. They also can update their credentials.
        They can request a password change if they don't have a
        registered email and if they do have a registered email,
        they can request a change through the system.

        Admin users can
            manage users (TODO: CRUD plus credential management)
            manage chores CRUD (almost done; buggy);
            assign chores to users (TODO)
            approve a chore marked by a general user as done

2.  What is the basic app flow?
    a. A general user signs up OR is added to the app by an admin
    b. An admin assigns chores to the user
    c. The user completes their chores and marks them as ready for review
    d. An admin verifies the work and if satisfactory, approves the work
    e. If/when the task is approved, the user is awarded the task points
    f. Users accumiulate points and are able to spend them on prizes

3.  TODOS
    a.  Chore creation and management (90%)
    b.  Assigning chores to users.
    b.  Admin user management dashboard (TODO)
    c.  User profile dashboard (TODO)
    d.  User point and prize system (TODO)
    e.  App onboarding
    f.  Maybe I really should look into soft deletes...

4.  I fiddled around with the app onboarding concept a bit earlier.
    The basic gist is we check for a .env file with credentials and
    use the credentials to check for a database/app schema and at
    least one admin user. If all these are found we should be able
    to assume we have a working app. If they're not found, we display
    a form seeking the information we need and get the defaults from
    the already existing $_ENV superglobal to finish out the app config.
    We would also possibly need to figure out how to run migrations
    outside of the context of the command line and then display a one-time
    form to register the first admin user.

5.  Bugs
    a.  I don't think I got the foreign keys right. I don't see them in
        workbench and when I deleted a chore that was assigned to a user,
        the join-table entry did not get removed as expected.
    b.  The state on the edit chore modal isn't working correctly. sometimes
        the fields are populated correctly, but other times they're not.
        I'm not sure why.


Thoughts/questions:

1.  Maybe I should decouple chore assignment from chore creation/editing?
2.  For now, I think I am going to keep a 1-1 relationship between users and chores
    i.e. no multi-assignment, so updating a chore would effectively transfer
    the chore assignment to another user. In the future, it may be pretty easy
    to have multi-assignments, but would be slightly more complex.
3.  For updating a chore to a new user, how do we find the specificchore-instance?
    We should have the chore id and the new user id. if (for now) a chore can only
    be assigned to one user, we should be able to get the core by id loading users
    to get the current user id and use chore_id and user_id to get that specific
    instance. We could then update the user_id for that user_chore instance.
4.  As it is currently, if the admin user specifies an assignee during chore creation,
    that's when we create the user_chore record in the database. From there on,
    that record can't be unassigned unless we make the user_id field nullable.