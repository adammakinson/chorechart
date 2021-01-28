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
    a.  Chore creation and management (DONE)
    b.  Assigning chores to a user (DONE)
    c.  User registration process (DONE)
    d.  Admin user management dashboard (DONE)
    e.  User profile dashboard (TODO)
    f.  User point and prize system (TODO)
    g.  App onboarding
    h.  Maybe I really should look into soft deletes... (Added soft deletes for users)
    i.  Creation of a home screen
    j.  User notification system
    k.  add a 404 forbidden error screen for routes the user shouldnt be able to 
        access instead of just booting them from the app.
    l.  Checkbox functionality
        1.  An admin assigns a chore to another user (could be another admin)
            I think we need an assignee, which would also be the reviewer.
        2.  The person to whom the chore is assigned gets the chore in their
            list and the checkmark icon. The checkmark icon here submits a
            state change indicating the chore is "ready for review" 
        3.  The admin (person who assigned the task) would then have the checkmark
            show up in their interface for the task. Checking this instance of
            the checkbox would be done after the assigner of the chore had
            manually verified that the task was completed to satisfaction.
        4.  I think all I need to do is add an assigner_id to the user_chores
            table because it makes sense that a chore can only be assined by
            one person. I think with that, everything is there for the remaining
            functionality.


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
    b.  general user is still able to see the add chore button in their chores list view


Thoughts/questions:

1.  Maybe I should decouple chore assignment from chore creation/editing?
2.  For now, I think I am going to keep a 1-1 relationship between users and chores
    i.e. no multi-assignment, so updating a chore would effectively transfer
    the chore assignment to another user. In the future, it may be pretty easy
    to have multi-assignments, but would be slightly more complex.
3.  For updating a chore to a new user, how do we find the specific chore-instance?
    We should have the chore id and the new user id. if (for now) a chore can only
    be assigned to one user, we should be able to get the core by id loading users
    to get the current user id and use chore_id and user_id to get that specific
    instance. We could then update the user_id for that user_chore instance.
4.  One thought is to use user_chores as a historical record type of table which
    wouldn't be hard to do. I could add a details column and put a JSON string
    in there indicating the chore and points awarded. Then, I think the onDelete cascade
    stuff may not be wanted anyway since we would want to retain everything in
    the user_chores table...
5.  The user management screen needs to give the admin the ability to 
        "remove" a user
        grant user roles
        generate a new password on behalf of a user
        edit the user's information

        Hrm... maybe a better way to go about it would be to generate a one-time access token?
        I think we can just have edit and delete buttons in the main view. The admin
        would open the edit modal to change anything on the user. It would just have
        sections with different buttons to do various things. The modal would just have the
        one close button in the upper right hand corner.


relationships
1.  A user can have many chores
2.  A chore could conceivably be assigned to many users
    In addition a chore could be assigned to different users at different times


checkmark states
    IF the user is an admin (i.e. they are viewing all chores)
        IF the chore was assinged to them by them (they are assinger and owner):
        then checking the checkbox should set inspection_ready=now(), inspected_on=now(), inspection_passed=true
        These params passed to the DB successfully (OK 200) should cause the checkbox to be green

        IF the chore is assinged to another individual by them, the checkbox should
        not be visible nor clickable while inspection_ready=null. Once inspection_ready
        is set, the checkbox should appear for the admin user and clicking on it
        should set inspected_on=now() and inspection_passed=true in the database and
        cause the checkbox to become green.

    IF the user is NOT an admin, they only need to see inspection_ready.
        IF inspection_ready=null, checkbox is gray. Clicking checkbox will send
        an inspection_ready=now() message to the server which will get updated on
        the Model and persisted to the DB. An OK 200 response will cause the checkbox
        to get a class coloring green.

