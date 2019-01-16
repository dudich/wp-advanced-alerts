<?php

/**
 * Created by PhpStorm.
 * User: dev
 * Date: 30.11.18
 * Time: 12:33
 */
class InitialFillNotificationTemplateTable extends NotificationModel {
	private $templates;

	public function __construct() {

		parent::__construct();
		$this->templates = [
			[
				'template_name'        => 'Email1',
				'notification_type_id' => 1,
				'destination_type_id'  => 1,
				'subject'              => ' Welcome to Notification!',
				'template_body'        => 'We are excited to have you join our network. Owners and managers at over 800 other buildings trust Notification for all their property needs and now you can enjoy seamless procurement as well. 

Get started posting your project today and whether you want to work with the vendors you already know or access any of our 20,000 licensed and vetted contractors, Notification has everything you need.

Property managers are saving 80% of their time and 20% on costs on average by using Notification. We look forward to delivering the same value to you.
 
Feel free to contact me directly with any questions or if you need assistance.
 
Regards,
Developer
CEO, Notification
123-456-7890',
				'description'          => 'Email 1 (Welcome)',
			],
			[
				'template_name'        => 'Email2',
				'notification_type_id' => 1,
				'destination_type_id'  => 1,
				'subject'              => 'Your Favorite Vendors',
				'template_body'        => 'Hi %%personal_name%%,

Notification has over 20,000 licensed contractors in our database, including many of the vendors you already know and love. Now, our team wants to make sure all of your favorite vendors are immediately accessible through Notification. Click here to add the vendors in your network to your Notification preferred list.

As you get started with Notification, feel free to check out our  <a href="https://notification.com/help/">tutorial videos</a> or contact us directly (<a href="mailto:support@notification.com">support@notification.com</a>) with any questions.
 
Regards,
Administrator
',
				'description'          => 'Email 2 (6 hours after sign-up)',

			],
			[
				'template_name'        => 'Email3',
				'notification_type_id' => 1,
				'destination_type_id'  => 1,
				'subject'              => 'Let’s Get Started!',
				'template_body'        => 'Hi %%personal_name%%,

We are excited to have you here on Notification with us. Let’s get you started with your first project. Are you bidding out any repairs or improvements currently? If you are, you can post them on Notification and start receiving great bids in no time. Check out our   <a href="http://dev.loc">user tutorial </a> for tips on creating a strong scope of work, guaranteed to attract top contractors.

You can also email support@notification.com or call us at 123-456-7890 with information about your project and our team will help create your scope immediately.

Sincerely,
Team Notification',
				'description'          => 'Email3 (no project posted 1 day after sign up)',

			],
			[
				'template_name'        => 'Email4',
				'notification_type_id' => 1,
				'destination_type_id'  => 1,
				'subject'              => 'Get Great Bids on Notification',
				'template_body'        => 'Hi %%personal_name%%,
 
How have you been? We’re reaching to see what upcoming projects you have and if we can help you get started. Let us know in this 1 minute survey (<a href="http://dev.loc">(http://dev.loc).</a>). 

The Notification support team is happy to help create your project scope as well – email support@notification.com or call us at 123-456-7890 with information about what you need done.

Sincerely,
Team Notification
',
				'description'          => 'Email 4 (no project posted 3 days after sign up)',

			],
			[
				'template_name'        => 'Email5',
				'notification_type_id' => 1,
				'destination_type_id'  => 1,
				'subject'              => 'Saving Time and Money with Notification',
				'template_body'        => 'Hi %%personal_name%%,

How are you? We noticed that you haven\'t posted a project in a while. Do your buildings need any work done? Let us know in this 1 minute survey <a href="http://dev.loc">(http://dev.loc).</a>

The average property manager spends 12.5 hours bidding out a project. On Notification, managers spend less than 1/5th of the time and save 20% on costs on average. Whether your next project is small or large, immediate or long term, Notification will help you save time and money. Post your next project <a href="https://notification.com/submit-project">here</a>.

Sincerely,
Team Notification',
				'description'          => 'Email 5 (send every 10 days after a user’s last project was posted – also send to users who have not posted any projects at all, every 10 days)',

			],
			[
				'template_name'        => 'Email6',
				'notification_type_id' => 1,
				'destination_type_id'  => 1,
				'subject'              => '%%project_name%% is Live!',
				'template_body'        => '<h1>Hi %%personal_name%%,</h1>
Congratulations, your project "%%project_name%%" has successfully published on Notification. You can review the project and keep track of contractor questions and bids here: %%project_link%%.

Remember to make sure that your project has everything it needs to attract the best bids. Watch this <a href="http://dev.loc">tutorial</a> to see how to post a great project.

Feel free to edit your project at any time. Our tutorial here (<a href="http://dev.loc">http://dev.loc</a>) can show you how. As always, we are eager to help you, so please feel free to contact us with any questions.

Regards,
Team Notification</p>',
				'description'          => 'Email 6 (project posted)',

			],
			[
				'template_name'        => 'Email7',
				'notification_type_id' => 1,
				'destination_type_id'  => 1,
				'subject'              => 'Here Come the Bids!',
				'template_body'        => 'Congratulations! Your project %%project_name%% received its first bid. Check out what this contractor is quoting you on <a href="https://notification.com">Notification.com</a>

Let’s keep up the momentum and get you more bids from the best vendors in the city. Invite more contractors by clicking here: <a href="https://notification.com">https://notification.com/</a>, or if you have a list of vendors you already know, be sure to add them here so they can submit proposals as well %%project_name%%.

Sincerely,
Team Notification',
				'description'          => 'Email 7 (after project is posted and first bid is received)',

			],
			[
				'template_name'        => 'Email8',
				'notification_type_id' => 1,
				'destination_type_id'  => 1,
				'subject'              => 'The Bids Are In!',
				'template_body'        => '<h1>Hi %%personal name%%</h1>,

<p>Great work! Your project has now received 5 bids — a 66% increase compared to buildings who are not using Notification. You are ahead of the curve and because of that, you are receiving better prices than anyone else. Now, you can easily compare these bids side by side on Notification, or export them to a pre-organized Excel report with a single click. Check out this 30-second video:<a href="http://dev.loc" target="_blank" rel="noopener"> http://dev.loc/</a> to see how.</p>

Sincerely,
Team Notification',
				'description'          => 'Email 8 (after project is posted and 5 bids are received)',

			],
			[
				'template_name'        => 'Email9',
				'notification_type_id' => 1,
				'destination_type_id'  => 1,
				'subject'              => 'Deadline Approaching',
				'template_body'        => 'Hi %%personal_name%%,
 
Your project, %%project_name%%, officially stops accepting bids tomorrow. You’ve received %%number_of_bids%% since you posted it. Have you found the right vendor? If not, feel free to extend the bid deadline and invite more contractors. Check out this <a href="http://dev.loc">video</a> to see how. Or, if you have a list of vendors you already know, be sure to add them here so they can submit proposals as well %%project_link%%.

Notification guarantees you the best bids – let us know if you need any help, and tell us how we\'re doing so far by filling out this <a href="http://dev.loc">30-second survey</a>. We really appreciate your feedback!

Sincerely,
Team Notification',
				'description'          => 'Email 9 (1 day before bid deadline is reached IF USER RECEIVED 3 BIDS OR LESS)',

			],
			[
				'template_name'        => 'Email10',
				'notification_type_id' => 1,
				'destination_type_id'  => 1,
				'subject'              => 'Deadline Approaching',
				'template_body'        => 'Hi %%personal_name%%,
 
Your project, %%project_name%%, officially stops accepting bids tomorrow. You’ve received %%number_of_bids%% since you posted it. Your project received %%percent_number_of_bids%%% more bids than projects not on Notification. 

Have you found the right vendor? If not, feel free to extend the bid deadline and invite more contractors. Check out this <a href="http://dev.loc">video</a> to see how. Or, if you have a list of vendors you already know, be sure to add them here so they can submit proposals as well %%href: %%.

Notification guarantees you the best bids - let us know if you need help, and tell us how we\'re doing so far by filling out this <a href="http://dev.loc">30-second survey</a>. We really appreciate your feedback!

Sincerely,
Team Notification',
				'description'          => 'Email 10 (1 day before bid deadline is reached IF USER RECEIVED MORE THAN 3 BIDS)',

			],
			[
				'template_name'        => 'Email11',
				'notification_type_id' => 1,
				'destination_type_id'  => 1,
				'subject'              => 'The bids are in!',
				'template_body'        => 'Hi %%personal_name%%,
 
Your project, %%project_name%%, received %%number_of_bids%% and has now passed the bidding deadline. Take a look at the project %%link_to_project%% on Notification and review the bids on directly on the website or export a pre-formatted Excel comparison sheet (watch this short <a href="http://dev.loc">video</a> to see how). 

After you’ve reviewed, make sure to accept the winning bid through Notification. When you do, the winning bidder will automatically be notified that their bid was accepted, and the other vendors will be told their bid was not chosen. No more calling up vendors manually to let them know! 

Or, if none of these bids look right, you can extend the bid deadline to continue receiving proposals. Watch this <a href="http://dev.loc">short video</a> to see how. 

Sincerely,
Team Notification
',
				'description'          => 'Email 11 (1 day after project deadline is reached, no bid chosen)',

			],
			[
				'template_name'        => 'Email12',
				'notification_type_id' => 1,
				'destination_type_id'  => 1,
				'subject'              => 'Did you choose a vendor?',
				'template_body'        => 'Hi %%personal_name%%,
 
Your project, %%project_name%%, received %%number_of_bids%%. Have you selected a vendor to work with yet? If so, be sure to accept their bid on Notification %%link_to_project%%. Projects can get started and completed more efficiently when a winning bid is chosen as soon as possible.

If none of these bids look right, you can also extend the bid deadline to continue receiving proposals (<a href="http://dev.loc">click here</a> to see how). Notification guarantees you the best bids – let us know how we can help.
 
Sincerely,
Team Notification',
				'description'          => 'Email 12 (3 days after project deadline is reached, no bid chosen)',

			],
			[
				'template_name'        => 'Email13',
				'notification_type_id' => 1,
				'destination_type_id'  => 1,
				'subject'              => 'Don’t Forget to Accept',
				'template_body'        => 'Hi %%personal_name%%,
 
It’s been a while since your project, %%project_name%%, received %%number_of_bids%%. Have you selected a vendor to work with yet? 

Don’t forget to accept their bid on Notification – that also lets other vendors know they haven’t won the bid so they aren’t waiting to find out. Vendors are also more likely to continue bidding on your future projects if prior projects were awarded more promptly, so do your best to review the bids and choose a winner as soon as you can. 

Sincerely, 
Team Notification',
				'description'          => 'Email 13 (each week after project deadline is reached, no bid chosen)',

			],
			[
				'template_name'        => 'Email14',
				'notification_type_id' => 1,
				'destination_type_id'  => 1,
				'subject'              => 'Congratulations!',
				'template_body'        => 'Hi %%personal_name%%,

You’ve chosen to go with %%contractor_name%% on your project, %%project_name%%. We are happy Notification was able to quickly connect you with this company and make it easy to get your project started.

Did you know that Notification also makes it easy to work with %%contractor_name%% during your project so that you can keep track of messages and files in one place? Check it out right here! <a href="http://dev.loc/">href: http://dev.loc</a> .

Sincerely,
Team Notification',
				'description'          => 'Email 14 (after a winning bid is chosen)',

			],
			[
				'template_name'        => 'Email15',
				'notification_type_id' => 1,
				'destination_type_id'  => 1,
				'subject'              => 'How’s your project going?',
				'template_body'        => 'Hi %%personal_name%%,

We hope your project, %%project_name%%, is going well! Let us know where it stands with this 1 question survey: <a href="http://dev.loc">http://dev.loc</a>  and let us know if there’s anything we can do to help.

Sincerely,
Team Notification',
				'description'          => 'Email 15 (1 week after winning bid is chosen)',

			],
			[
				'template_name'        => 'Email16',
				'notification_type_id' => 1,
				'destination_type_id'  => 1,
				'subject'              => '%%project_name%% has been archived',
				'template_body'        => 'Hi %%personal_name%%,

Your project, %%project_name%%, has been archived successfully. You can review this project, and all the associated questions, bids, and communications, at any time by going to the “Archived Projects” section of your dashboard or by clicking here: %%project_link%%.

Sincerely,
Team Notification',
				'description'          => 'Email 16 (project archived/deleted)',

			],
			[
				'template_name'        => 'Email17',
				'notification_type_id' => 1,
				'destination_type_id'  => 1,
				'subject'              => 'New bid on Notification',
				'template_body'        => 'Hi %%personal_name%%,

Congratulations! You have received a new bid on your project, %%project_name%%. You can view the full details of the bid here: %%project_link%%.

Sincerely, 
Team Notification',
				'description'          => 'Email 17 (bid received, all bids after first bid)',

			],

			// Contractors

			[
				'template_name'        => 'Email1',
				'notification_type_id' => 1,
				'destination_type_id'  => 2,
				'subject'              => 'Welcome to Notification!',
				'template_body'        => 'It is a pleasure to have you join our network! We are excited to connect you with our community of property managers and owners at over 800 buildings here in New York City. They are constantly looking to hire great vendors like you for maintenance, repairs, and improvements at their properties.

Notification gives you the best opportunity to win contracts on high value work that buildings are only sharing here. Check out what you can bid on at Notification.com: <a href="https://notification.com/">https://notification.com/</a>.

If you need any assistance, our team is available 24/7 - please contact us or call me directly with any questions.

Best,
Team Notification',
				'description'          => 'Email 1: signs up',

			],

			[
				'template_name'        => 'Email2',
				'notification_type_id' => 1,
				'destination_type_id'  => 2,
				'subject'              => 'Verify Your Notification Account',
				'template_body'        => 'Hi %%personal_name%%,

We are thrilled to have you here on Notification. We want your business to succeed and that’s why we made Notification, the best platform to bring contractors valuable business. 
	
Put your business on top by verifying your profile. Vendors are 5 times more likely to win bids when their account is verified – watch this <a href="http://dev.loc"> 2-minute video</a> to see how to do it.
	
Sincerely,
Team Notification',
				'description'          => 'Email 2: 1 day after sign up',

			],
			[
				'template_name'        => 'Email3',
				'notification_type_id' => 1,
				'destination_type_id'  => 2,
				'subject'              => 'Congratulations! You’re Invited to %%project_name%%',
				'template_body'        => 'Hi %%personal_name%%,

Congratulations! A property manager has invited you to submit a proposal on their project, %%project_name%%. You can view this project here: %%project_link%%

Notification is eager to help you win this opportunity, so we’ve made it easy to ask questions and get the information you need to submit your best proposal. Take a look <a href="http://dev.loc">here</a>. Remember to submit a detailed proposal so that the project owner knows exactly what you’re able to deliver.

And feel free to reach out to us at any time for assistance.

Sincerely,
Team Notification',
				'description'          => 'Email 3: invitation to bid',

			],
			[
				'template_name'        => 'Email4',
				'notification_type_id' => 1,
				'destination_type_id'  => 2,
				'subject'              => 'Don’t Forget to Submit Your Proposal',
				'template_body'        => 'Hi %%personal_name%%,

A property manager invited you to submit a proposal on their project, %%project_name%%, a couple days ago %%project_link%%. Did you get a chance to take a look? You can always let them know if you intend to submit a proposal %%project_link%% and if you do, make sure to get your proposal in before the deadline — this is a great opportunity!

Also remember to submit a thorough proposal so that the project owner knows exactly what you’re able to deliver. And if you have any questions about the project, you can submit them directly through Notification. Check out this 30-second video: <a href="http://dev.loc">http://dev.loc</a> to see how.

Feel free to reply to this email or reach out at any time for assistance.

Sincerely,
Team Notification',
				'description'          => 'Email 4: 2 days after bid invitation and no bid from vendor',

			],
			[
				'template_name'        => 'Email5',
				'notification_type_id' => 1,
				'destination_type_id'  => 2,
				'subject'              => 'Time is Running Out',
				'template_body'        => 'Hi %%personal_name%%,

A property manager invited you to submit a proposal on their project, %%project_name%%, last week. Have you been able to prepare your proposal? Time is running out before the deadline on %%bidding_end_date%% and you don’t want to miss this great opportunity %%project_link%%!

Remember to submit a thorough proposal so that the project owner knows exactly what you’re able to deliver. And if you have any questions about the project, you can submit them directly through Notification. Check out this 30-second video:<a href="http://dev.loc"> http://dev.loc</a> see how.

Feel free to reach out to us at any time for assistance.

Sincerely,
Team Notification',
				'description'          => 'Email 5: 1 week after bid invitation and no bid',

			],
			[
				'template_name'        => 'Email6',
				'notification_type_id' => 1,
				'destination_type_id'  => 2,
				'subject'              => 'Deadline Tomorrow',
				'template_body'        => 'Hi %%personal_name%%,

A property manager invited you to submit a proposal on their project, %%project_name%%, %%project_link%%, and tomorrow is the deadline. Time is running out to submit a proposal on this great opportunity. Don’t miss this chance to win a valuable contract.

Remember to submit a thorough proposal so that the project owner knows exactly what you’re able to deliver. If you’re having any trouble submitting your proposal, please don’t hesitate to reach out to our team for assistance.

Sincerely,
Team Notification',
				'description'          => 'Email 6: 1 day before bid deadline reached',

			],
			[
				'template_name'        => 'Email7',
				'notification_type_id' => 1,
				'destination_type_id'  => 2,
				'subject'              => 'Updates from the Project Owner',
				'template_body'        => 'Hi %%personal_name%%,

We wanted to update you on the project, %%project_name%%, %%project_link%%, that you recently submitted a proposal on. The building manager is still reviewing the proposal and getting ready to decide on next steps. Thank you again for your patience and we’ll continue to update you as they make progress on their decision.

In the meantime, feel free to check out other available projects on Notification.com, and let us know if we can be of assistance in any way!

Sincerely,
Team Notification',
				'description'          => 'Email 7: 3 days after bid deadline reached and no bid is selected (sent to bidders only)',

			],
			[
				'template_name'        => 'Email8',
				'notification_type_id' => 1,
				'destination_type_id'  => 2,
				'subject'              => 'Updates from the Project Owner',
				'template_body'        => 'Hi %%personal_name%%,

We wanted to update you on the project, %%project_name%%, %%project_link%%, that you recently submitted a proposal on. The building manager recently let us know that they are still reviewing the proposal. Thank you again for your patience and we’ll continue to update you as they make progress on their decision.

In the meantime, feel free to check out other available projects on Notification.com, and let us know if we can be of assistance in any way!

Sincerely,
Team Notification',
				'description'          => 'Email 8: 1 week after bid deadline reached and no bid is selected (sent to bidders only)',

			],
			[
				'template_name'        => 'Email9',
				'notification_type_id' => 1,
				'destination_type_id'  => 2,
				'subject'              => 'Updates from the Project Owner',
				'template_body'        => 'Hi %%personal_name%%,

We wanted to update you on the project, %%project name%% %%project_link%%, that you recently submitted a proposal on. The building manager apologizes for the delay on this project and will be in touch soon with their decision for the next steps. Thank you again for your patience and we’ll continue to update you as they make progress on their decision.

Sincerely,
Team Notification',
				'description'          => 'Email 9: 2 weeks (and every week thereafter) after bid deadline reached and no bid is selected (sent to bidders only)',

			],
			[
				'template_name'        => 'Email10',
				'notification_type_id' => 1,
				'destination_type_id'  => 2,
				'subject'              => 'Congratulations! Your Proposal Won',
				'template_body'        => 'Hi %%personal_name%%,

Congratulations! The building manager for the project %%project_name%%, %%project_link%% has selected your proposal. We are excited that Notification has been able to help you secure this contract. Now, you should get started on this project as soon as possible. You can communicate with the property manager directly on Notification.

If you\'re interested in gaining access to even more great opportunities, remember that you can advertise your business directly to building owners and managers through Notification\'s newsletter. Contact us at <a href="mailto:team@notification.com">team@notification.com</a> or reply to this email for more information; our team would be happy to help create your ad and get it published in no time.

Also, let us know how you felt about the bidding process so we can continue to improve your experience %%survey%%. Good luck on this project and be on the lookout for more opportunities on Notification.

Sincerely,
Team Notification',
				'description'          => 'Email 10: bid is selected',

			],
			[
				'template_name'        => 'Email11',
				'notification_type_id' => 1,
				'destination_type_id'  => 2,
				'subject'              => 'Updates from a Project Owner',
				'template_body'        => 'Hi %%personal_name%%,

%%company_name_of_project_owner%% has selected a different contractor to work on their project, %%project_name%%, %%project_link%%. Nevertheless, they sincerely thank you for the time and effort you have taken over the preparation and submission of your proposal.

They personally asked us to share this message with you:
"Thank you for your bid on our project. We appreciate the time you put into considering this and hope that we can work together some time in the future."

Don’t worry about this project, Notification has many other great opportunities for you: %%link_to_available_projects%%. And to make sure your proposal was not in vain, we went ahead and prepared you an after-project report that details how far off you were from the winning proposal and tips to increase your chances next time. Request your report here: <a href="http://dev.loc">http://dev.loc</a>

Also, please let us know how you felt about the bidding process so we can continue to improve your experience %%survey%%.

Sincerely,
Team Notification',
				'description'          => 'Email 11: bid is not selected',

			],
			// %%number_of_bids%% with word bid/bids
			[
				'template_name'        => 'Email12',
				'notification_type_id' => 1,
				'destination_type_id'  => 2,
				'subject'              => 'Thank you for your bid!',
				'template_body'        => 'Hi %%personal_name%%,
 
Thank you for submitting your proposal on %%project_link%%. The property manager will be in touch with soon if they have questions. 
 
As always, Notification is proud to bring you the best leads in the business. While this project is in review, feel free to check out other available projects on Notification.com!

Sincerely,
Team Notification',
				'description'          => 'Email 12: after a bid is submitted',

			],
			[
				'template_name'        => 'Email13',
				'notification_type_id' => 1,
				'destination_type_id'  => 2,
				'subject'              => 'Update about your recent bid',
				'template_body'        => 'Hi %%personal_name%%,

%%project_owner%% has indicated that they intend to award the %%project_name%% project by %%selection_date%%.
%%owner_message%%',
				'description'          => 'Email 13: when a bid acceptance date is set',

			],
			[
				'template_name'        => 'Email14',
				'notification_type_id' => 1,
				'destination_type_id'  => 2,
				'subject'              => 'You have a site visit scheduled for tomorrow for %%project_name%%',
				'template_body'        => '<p>Hi, %%personal_name%%</p>
  <p>This is a reminder from Notification that you have a site visit scheduled for tomorrow at %%site_visit_date%% located at %%address%% for %%project_link%%. If you have any questions, contact %%site_visit_person%% at %%site_visit_phone%% who is overseeing the site visit.</p>
  <p>Best regards, <br><a href="//notification.com">Team Notification</a> </p>',

				'description' => 'function: claim_profile_admin_email',
			],

			// Emails sent from functions.php

			[
				'template_name'        => 'Email1',
				'notification_type_id' => 1,
				'destination_type_id'  => 3,
				'subject'              => 'Profile Claimed by Contractor.',
				'template_body'        => "<p>Hi, </p>
				<p>Claim profile form was submitted. Please see form details below: </p>
				<p>Profile Claimed: %%claim_profile_company_name%%</p>
				<p>First Name: %%first_name%%</p>
				<p>Last Name: %%last_name%%</p>
				<p>Email: %%user_email%%</p>
				<p>Telephone: %%telephone%%</p>
				<p>Regards, <br>%%blogname%%</p>",

				'description' => 'Email1: Profile Claimed by Contractor.',
			],
			[
				'template_name'        => 'Email2',
				'notification_type_id' => 1,
				'destination_type_id'  => 3,
				'subject'              => 'New message from %%from_company_name%% on %%blogname%%',
				'template_body'        => '<p>%%from_company_name%% sent you a message:</p>
				<br>
				<p>%%message%%</p>
				<p>You can respond to this message by replying to this email.</p>',

				'description' => 'function contact_profile_email',
			],
			[
				'template_name'        => 'Email3',
				'notification_type_id' => 1,
				'destination_type_id'  => 3,
				'subject'              => 'Site visit accepted by contractor.',
				'template_body'        => '<p>Hi, </p>
				<p>%%freelance%% has signed up to attend your site visit on %%date%% for the project %%project_link%%.</p>
				<p>Regards, <br>%%blogname%%</p>',

				'description' => 'function employer_site_visit_email',
			],
			[
				'template_name'        => 'Email4',
				'notification_type_id' => 1,
				'destination_type_id'  => 3,
				'subject'              => 'A contractor has cancelled their site visit',
				'template_body'        => '<p>Hi, </p>
				<p>%%freelance%% has chosen not to participate in a site visit on %%date%% for the project %%project_link%%.</p>
				<p>Regards, <br>%%blogname%%</p>',

				'description' => 'function: employer_cancel_site_visit_email',
			],
			[
				'template_name'        => 'Email5',
				'notification_type_id' => 1,
				'destination_type_id'  => 3,
				'subject'              => 'Site visit accepted.',
				'template_body'        => '<p>Hi, </p>
				<p>You have signed up to attend site visit on %%date%% for the project %%project_link%%.</p>
				<p>Regards, <br>%%blogname%%</p>',

				'description' => 'function: freelancer_site_visit_email',
			],
			[
				'template_name'        => 'Email6',
				'notification_type_id' => 1,
				'destination_type_id'  => 3,
				'subject'              => '%%company_name%% has updated the details for %%project_name%%',
				'template_body'        => '<p>%%company_name%% has updated the details for %%project_link%%.</p>
				<p>Please refer to the changes before you submit your bid.</p>
				<p>Regards, <br>%%blogname%%</p>',

				'description' => 'function: freelancer_proj_update_email',
			],
//            [
//                'template_name' => 'Email7',
//                'notification_type_id' => 1,
//                'destination_type_id' => 3,
//                'subject' => 'You have a site visit scheduled for tomorrow for %%project_name%%',
//                'template_body' => '<p>Hi, </p>
//				<p>this is a reminder from %%blogname%% that you have a site visit scheduled for tomorrow at %%date%% at %%address%% for %%link%%</p>
//				<p>Regards, <br>%%blogname%%</p>',
//
//                'description' => 'function: freelancer_proj_remind_site_visit',
//            ],
			[
				'template_name'        => 'Email7',
				'notification_type_id' => 1,
				'destination_type_id'  => 3,
				'subject'              => 'New Message from %%company_name%% on Notification',
				'template_body'        => '<p>%%company_name%% has commented on the project %%project_name%%:</p>
				<p>%%comment_text%%</p>
				<p>Click here to view the project %%project_link%% and be sure to update your bid if necessary.</p>
				<p>All the best, <br>%%blogname%%</p>',

				'description' => 'function: freelancer_proj_comment_update_email',
			],
			[
				'template_name'        => 'Email8',
				'notification_type_id' => 1,
				'destination_type_id'  => 3,
				'subject'              => 'A New Vendor Intends to bid on your project.',
				'template_body'        => '<p>Hi, </p>
				<p>%%freelance%% has indicated that they intend to submit a bid on your project, %%project_link%%. Feel free to communicate with them on %%blogname%%.</p>
				<p>Regards, <br>%%blogname%%</p>',

				'description' => 'function: employer_bid_intents_email',
			],
			[
				'template_name'        => 'Email9',
				'notification_type_id' => 1,
				'destination_type_id'  => 3,
				'subject'              => '%%project_description%%. Project at %%project_location%%',
				'template_body'        => '<p>Hi %%contact_first_name%%,</p>
					<p>We spoke earlier regarding the %%project_description%% project at %%project_address%%. The property manager is looking to get a proposal by %%project_deadline%%.</p>
					<p>They have posted the project on Notification.com, which helps property managers send their repair and maintenance projects to top contractors. You can view all the information at the link below:</p>
					<p>%%project_link%%</p>
					<p>Please let me know if you have any questions or have trouble accessing the site.</p>
					</br>
					Best,<br>
					%%project_manager%%',
				'description'          => 'function: add_user_from_crm',
			],

			// Old mailing system core emails

			[
				'template_name'        => 'Email1',
				'notification_type_id' => 1,
				'destination_type_id'  => 4,
				'subject'              => '%%blog_name%% New Private Message From %%sender%%',
				'template_body'        => '<h2>Hello %%display_name%%</h2>,

<p>You have just received the following message from %%sender%%</p>
</p>|--------------------------------------------------------------------------------------------------|</p>
<p>%%message%%</p>
<p>|--------------------------------------------------------------------------------------------------|</p>
<p>You can answer by replying to this email %%sender_link%%. </p>
<p>Sincerely,</p>
<p>%%blogname%%</p>',

				'description' => 'function inbox_mail - wp-content/themes/freelanceengine/includes/aecore/class-ae-mailing.php',
			],
			[
				'template_name'        => 'Email2',
				'notification_type_id' => 1,
				'destination_type_id'  => 4,
				'subject'              => '%%blogname%% Password Reset',
				'template_body'        => '<h2>Hello %%display_name%%,</h2>

<p>You have just sent a request to recover the password associated with your account in %%blogname%%.</p> 
<p>If you did not make this request, please ignore this email; otherwise, click the link below to create your new password:</p>
<p>%%activate_url%%</p>
<p>Regards,
%%blogname%%</p>',

				'description' => 'function forgot_mail. aecore/class-ae-mailing.php',
			],
			[
				'template_name'        => 'Email12',
				'notification_type_id' => 1,
				'destination_type_id'  => 4,
				'subject'              => 'New Project For You Today!',
				'template_body'        => '<h2>Hi there,</h2>

<p>There is a new job for you today. Hurry apply for this project %%project_link%% and get everything started.</p>

<p>Hope you have a highly effective Day</p>',

				'description' => 'function: new_project_of_category. wp-content/themes/freelanceengine/includes/mailing.php (789)',
			],
			[
				'template_name'        => 'Email13',
				'notification_type_id' => 1,
				'destination_type_id'  => 4,
				'subject'              => 'A contractor has canceled a bid on Your project %%project_name%%',
				'template_body'        => '<p>Hello %%display_name%%,</p>
                                    <p>The Freelancer is canceled a bid on the project : %%project_link%%.</p>
                                    <p>Sincerely,</p>
                                    <p>%%blogname%%</p>',

				'description' => '',
			],
			[
				'template_name'        => 'Email18',
				'notification_type_id' => 1,
				'destination_type_id'  => 4,
				'subject'              => 'You have successfully changed your password',
				'template_body'        => '<h2>Hello %%display_name%%,</h2>

<p>You have successfully changed your password. Click this link  %%site_url%% to login to your %%blogname%%\'s account.</p>

<p>Sincerely,</p>
%%blogname%%',

				'description' => '',
			],
			[
				'template_name'        => 'Email19',
				'notification_type_id' => 1,
				'destination_type_id'  => 4,
				'subject'              => 'You have a new message on %%project_name%% workspace.',
				'template_body'        => '<h2>Hello %%display_name%%,</h2>

<p>You have a new message on project %%project_name%%. Here is the message details:</p>

%%message%%

<p>You can view all message in %%workspace%%</p>

<p>Sincerely,</p>
<p>%%blogname%%</p>',

				'description' => '',
			],

		];
	}

	public function initialFillNotificationTemplatesTable() {
		foreach ( $this->templates as $template ) {
			$this->db->insert( $this->notification_templates, $template, [
				'%s',
				'%d',
				'%d',
				'%s',
				'%s',
				'%s'
			] );
		}
	}
}
