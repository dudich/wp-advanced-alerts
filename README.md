WordPress plugin that sends email notification on various events and condition.

Each event is associated with specific email template that can be edited in admin and support variables. E.g.
```
  <h1>Hi %%personal_name%%,</h1>
  Congratulations, your project "%%project_name%%" has successfully published on Notification. You can review the project and keep track of contractor questions and bids here: %%project_link%%.

  Remember to make sure that your project has everything it needs to attract the best bids. Watch this <a href="https://www.youtube.com/watch?v=222">tutorial</a> to see how to post a great project.

  Feel free to edit your project at any time. Our tutorial here (<a href="https://www.youtube.com/watch?v=111">https://www.youtube.com/watch?v=fghf</a>) can show you how. As always, we are eager to help you, so please feel free to contact us with any questions.

  Regards,
  %%site_admin_name%%
```
There are multiple type of events:
- single immediate events
- delayed events
- recurring events 

Plugin is integrated with logging system that allows admin to monitor events and notifications. 

There is dev mode support that disables actual email sending and leaves only logging to simplify system configuration.
