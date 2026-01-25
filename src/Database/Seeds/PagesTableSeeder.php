<?php
namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'pages';
        $table_name_langs = 'page_langs';
        $date = date('Y-m-d H:i:s');


        $pages = array(
            array('id' => '1','site_id' => '1','microsite_id' => '0','platform_id' => '1','category_id' => '9','alias' => 'TEST','exclude_in_listing' => '0','content_type' => 'page','position' => '3','link_rewrite' => 'tnc','link_navigation' => NULL,'menu_placement' => 'bottom','header_content' => NULL,'footer_content' => NULL,'insert_by' => '1','update_by' => '1','required_login' => '0','publish_status' => '1','created_at' => NULL,'enable_comments' => '0','updated_at' => '2020-07-02 13:37:37','deleted_at' => NULL),
            array('id' => '2','site_id' => '1','microsite_id' => '0','platform_id' => NULL,'category_id' => '9','alias' => 'PRIVACY_POLICY','exclude_in_listing' => '0','content_type' => 'page','position' => '4','link_rewrite' => 'privacy-policy','link_navigation' => NULL,'menu_placement' => 'bottom','header_content' => NULL,'footer_content' => NULL,'insert_by' => '1','update_by' => '1','required_login' => '0','publish_status' => '1','created_at' => NULL,'enable_comments' => '0','updated_at' => '2020-07-05 09:12:13','deleted_at' => NULL),
            array('id' => '3','site_id' => '1','microsite_id' => '0','platform_id' => NULL,'category_id' => '2','alias' => 'TEST_BLOG','exclude_in_listing' => '0','content_type' => 'blog','position' => '4','link_rewrite' => 'test-blog','link_navigation' => NULL,'menu_placement' => NULL,'header_content' => NULL,'footer_content' => NULL,'insert_by' => '1','update_by' => '1','required_login' => '0','publish_status' => '1','created_at' => '2020-07-11 05:50:06','enable_comments' => '1','updated_at' => NULL,'deleted_at' => NULL)
        );


        $page_langs = array(
            array('page_id' => '1','lang_id' => '1','name' => 'Terms and Conditions','title' => 'Terms and Conditions','description' => NULL,
                'page_content' =>
                    "<p>
	Welcome to&nbsp;Website Name!
</p>
<p>
	These terms and conditions outline the rules and regulations for the use of&nbsp;Company Name's Website, located at&nbsp;Website.com.
</p>
<p>
	By accessing this website we assume you accept these terms and conditions. Do not continue to use&nbsp;Website Name&nbsp;if you do not agree to take all of the terms and conditions stated on this page.
</p>
<p>
	The following terminology applies to these Terms and Conditions, Privacy Statement and Disclaimer Notice and all Agreements: &ldquo;Client&rdquo;, &ldquo;You&rdquo; and &ldquo;Your&rdquo; refers to you, the person log on this website and compliant to the Company's terms and conditions. &ldquo;The Company&rdquo;, &ldquo;Ourselves&rdquo;, &ldquo;We&rdquo;, &ldquo;Our&rdquo; and &ldquo;Us&rdquo;, refers to our Company. &ldquo;Party&rdquo;, &ldquo;Parties&rdquo;, or &ldquo;Us&rdquo;, refers to both the Client and ourselves. All terms refer to the offer, acceptance and consideration of payment necessary to undertake the process of our assistance to the Client in the most appropriate manner for the express purpose of meeting the Client's needs in respect of provision of the Company's stated services, in accordance with and subject to, prevailing law of Netherlands. Any use of the above terminology or other words in the singular, plural, capitalization and/or he/she or they, are taken as interchangeable and therefore as referring to same.
</p>
<h3>
	Cookies
</h3>
<p>
	We employ the use of cookies. By accessing&nbsp;Website Name, you agreed to use cookies in agreement with the&nbsp;Company Name's Privacy Policy.
</p>
<p>
	Most interactive websites use cookies to let us retrieve the user's details for each visit. Cookies are used by our website to enable the functionality of certain areas to make it easier for people visiting our website. Some of our affiliate/advertising partners may also use cookies.
</p>
<h3>
	License
</h3>
<p>
	Unless otherwise stated,&nbsp;Company Name&nbsp;and/or its licensors own the intellectual property rights for all material on&nbsp;Website Name. All intellectual property rights are reserved. You may access this from&nbsp;Website Name&nbsp;for your own personal use subjected to restrictions set in these terms and conditions.
</p>
<p>
	You must not:
</p>
<ul>
	<li>
		Republish material from&nbsp;Website Name
	</li>
	<li>
		Sell, rent or sub-license material from&nbsp;Website Name
	</li>
	<li>
		Reproduce, duplicate or copy material from&nbsp;Website Name
	</li>
	<li>
		Redistribute content from&nbsp;Website Name
	</li>
</ul>
<p>
	This Agreement shall begin on the date hereof.
</p>
<p>
	Parts of this website offer an opportunity for users to post and exchange opinions and information in certain areas of the website.&nbsp;Company Name&nbsp;does not filter, edit, publish or review Comments prior to their presence on the website. Comments do not reflect the views and opinions of&nbsp;Company Name,its agents and/or affiliates. Comments reflect the views and opinions of the person who post their views and opinions. To the extent permitted by applicable laws,&nbsp;Company Name&nbsp;shall not be liable for the Comments or for any liability, damages or expenses caused and/or suffered as a result of any use of and/or posting of and/or appearance of the Comments on this website.
</p>
<p>
	Company Name&nbsp;reserves the right to monitor all Comments and to remove any Comments which can be considered inappropriate, offensive or causes breach of these Terms and Conditions.
</p>
<p>
	You warrant and represent that:
</p>
<ul>
	<li>
		You are entitled to post the Comments on our website and have all necessary licenses and consents to do so;
	</li>
	<li>
		The Comments do not invade any intellectual property right, including without limitation copyright, patent or trademark of any third party;
	</li>
	<li>
		The Comments do not contain any defamatory, libelous, offensive, indecent or otherwise unlawful material which is an invasion of privacy
	</li>
	<li>
		The Comments will not be used to solicit or promote business or custom or present commercial activities or unlawful activity.
	</li>
</ul>
<p>
	You hereby grant&nbsp;Company Name&nbsp;a non-exclusive license to use, reproduce, edit and authorize others to use, reproduce and edit any of your Comments in any and all forms, formats or media.
</p>
<h3>
	Hyperlinking to our Content
</h3>
<p>
	The following organizations may link to our Website without prior written approval:
</p>
<ul>
	<li>
		Government agencies;
	</li>
	<li>
		Search engines;
	</li>
	<li>
		News organizations;
	</li>
	<li>
		Online directory distributors may link to our Website in the same manner as they hyperlink to the Websites of other listed businesses; and
	</li>
	<li>
		System wide Accredited Businesses except soliciting non-profit organizations, charity shopping malls, and charity fundraising groups which may not hyperlink to our Web site.
	</li>
</ul>
<p>
	These organizations may link to our home page, to publications or to other Website information so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products and/or services; and (c) fits within the context of the linking party's site.
</p>
<p>
	We may consider and approve other link requests from the following types of organizations:
</p>
<ul>
	<li>
		commonly-known consumer and/or business information sources;
	</li>
	<li>
		dot.com community sites;
	</li>
	<li>
		associations or other groups representing charities;
	</li>
	<li>
		online directory distributors;
	</li>
	<li>
		internet portals;
	</li>
	<li>
		accounting, law and consulting firms; and
	</li>
	<li>
		educational institutions and trade associations.
	</li>
</ul>
<p>
	We will approve link requests from these organizations if we decide that: (a) the link would not make us look unfavorably to ourselves or to our accredited businesses; (b) the organization does not have any negative records with us; (c) the benefit to us from the visibility of the hyperlink compensates the absence of&nbsp;Company Name; and (d) the link is in the context of general resource information.
</p>
<p>
	These organizations may link to our home page so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products or services; and (c) fits within the context of the linking party's site.
</p>
<p>
	If you are one of the organizations listed in paragraph 2 above and are interested in linking to our website, you must inform us by sending an e-mail to&nbsp;Company Name. Please include your name, your organization name, contact information as well as the URL of your site, a list of any URLs from which you intend to link to our Website, and a list of the URLs on our site to which you would like to link. Wait 2-3 weeks for a response.
</p>
<p>
	Approved organizations may hyperlink to our Website as follows:
</p>
<ul>
	<li>
		By use of our corporate name; or
	</li>
	<li>
		By use of the uniform resource locator being linked to; or
	</li>
	<li>
		By use of any other description of our Website being linked to that makes sense within the context and format of content on the linking party's site.
	</li>
</ul>
<p>
	No use of&nbsp;Company Name's logo or other artwork will be allowed for linking absent a trademark license agreement.
</p>
<h3>
	iFrames
</h3>
<p>
	Without prior approval and written permission, you may not create frames around our Webpages that alter in any way the visual presentation or appearance of our Website.
</p>
<h3>
	Content Liability
</h3>
<p>
	We shall not be hold responsible for any content that appears on your Website. You agree to protect and defend us against all claims that is rising on your Website. No link(s) should appear on any Website that may be interpreted as libelous, obscene or criminal, or which infringes, otherwise violates, or advocates the infringement or other violation of, any third party rights.
</p>
<h3>
	Reservation of Rights
</h3>
<p>
	We reserve the right to request that you remove all links or any particular link to our Website. You approve to immediately remove all links to our Website upon request. We also reserve the right to amen these terms and conditions and it's linking policy at any time. By continuously linking to our Website, you agree to be bound to and follow these linking terms and conditions.
</p>
<h3>
	Removal of links from our website
</h3>
<p>
	If you find any link on our Website that is offensive for any reason, you are free to contact and inform us any moment. We will consider requests to remove links but we are not obligated to or so or to respond to you directly.
</p>
<p>
	We do not ensure that the information on this website is correct, we do not warrant its completeness or accuracy; nor do we promise to ensure that the website remains available or that the material on the website is kept up to date.
</p>
<h3>
	Disclaimer
</h3>
<p>
	To the maximum extent permitted by applicable law, we exclude all representations, warranties and conditions relating to our website and the use of this website. Nothing in this disclaimer will:
</p>
<ul>
	<li>
		limit or exclude our or your liability for death or personal injury;
	</li>
	<li>
		limit or exclude our or your liability for fraud or fraudulent misrepresentation;
	</li>
	<li>
		limit any of our or your liabilities in any way that is not permitted under applicable law; or
	</li>
	<li>
		exclude any of our or your liabilities that may not be excluded under applicable law.
	</li>
</ul>
<p>
	The limitations and prohibitions of liability set in this Section and elsewhere in this disclaimer: (a) are subject to the preceding paragraph; and (b) govern all liabilities arising under the disclaimer, including liabilities arising in contract, in tort and for breach of statutory duty.
</p>
<p>
	As long as the website and the information and services on the website are provided free of charge, we will not be liable for any loss or damage of any nature.
</p>"
                ,'link_relation' => NULL,'target' => NULL,'active_key' => 'test','meta_title' => 'Terms and Conditions','meta_keywords' => 'Terms and Conditions','meta_description' => 'Terms and Conditions','meta_robots' => 'index, follow','meta_canonical' => NULL,'created_at' => '2020-06-30 12:40:36','updated_at' => '2020-07-02 13:37:37','deleted_at' => NULL),
            array('page_id' => '2','lang_id' => '1','name' => 'Privacy Policy','title' => 'Privacy Policy','description' => NULL,'page_content' => "
<p>Your privacy is important to us. It is SiteName's policy to respect your privacy regarding any information we may collect from you across our website, <a href='https://www.hashtagcms.org'>https://www.hashtagcms.org</a>, and other sites we own and operate.</p>
<p>We only ask for personal information when we truly need it to provide a service to you. We collect it by fair and lawful means, with your knowledge and consent. We also let you know why we&rsquo;re collecting it and how it will be used.</p>
<p>We only retain collected information for as long as necessary to provide you with your requested service. What data we store, we&rsquo;ll protect within commercially acceptable means to prevent loss and theft, as well as unauthorized access, disclosure, copying, use or modification.</p>
<p>We don&rsquo;t share any personally identifying information publicly or with third-parties, except when required to by law.</p>
<p>Our website may link to external sites that are not operated by us. Please be aware that we have no control over the content and practices of these sites, and cannot accept responsibility or liability for their respective privacy policies.</p>
<p>You are free to refuse our request for your personal information, with the understanding that we may be unable to provide you with some of your desired services.</p>
<p>Your continued use of our website will be regarded as acceptance of our practices around privacy and personal information. If you have any questions about how we handle user data and personal information, feel free to contact us.</p>
<p>This policy is effective as of 2 Jan 2022.</p>",'link_relation' => NULL,'target' => NULL,'active_key' => 'privacy-policy','meta_title' => NULL,'meta_keywords' => NULL,'meta_description' => NULL,'meta_robots' => NULL,'meta_canonical' => NULL,'created_at' => '2020-07-02 10:37:07','updated_at' => '2020-07-05 09:25:25','deleted_at' => NULL),
            array('page_id' => '3','lang_id' => '1','name' => 'Test Blog','title' => 'Test Blog','description' => 'This is a test blog.','page_content' => 'This is test blog content.','link_relation' => NULL,'target' => NULL,'active_key' => 'test-blog','meta_title' => NULL,'meta_keywords' => NULL,'meta_description' => NULL,'meta_robots' => NULL,'meta_canonical' => NULL,'created_at' => '2020-07-11 10:47:26','updated_at' => '2020-07-11 10:47:26','deleted_at' => NULL)
        );

        $page_langs_hi = array(
            array('page_id' => '1','lang_id' => '2','name' => 'Terms and Conditions','title' => 'Terms and Conditions','description' => NULL,
                'page_content' =>
                    "<p>
वेबसाइट के नाम में आपका स्वागत है!
</p>
<p>
ये नियम और शर्तें &nbsp;वेबसाइट.कॉम पर स्थित कंपनी नाम की वेबसाइट के उपयोग के लिए नियमों और विनियमों की रूपरेखा तैयार करती हैं।
</p>
<p>
इस वेबसाइट तक पहुँचने से हम मानते हैं कि आप इन नियमों और शर्तों को स्वीकार करते हैं। यदि आप इस पृष्ठ पर बताए गए सभी नियमों और शर्तों को मानने के लिए सहमत नहीं हैं, तो&nbsp;वेबसाइट के नाम&nbsp;का उपयोग जारी न रखें।
</p>
<p>
निम्नलिखित शब्दावली इन नियमों और शर्तों, गोपनीयता कथन और अस्वीकरण नोटिस और सभी अनुबंधों पर लागू होती है: &ldquo;ग्राहक&rdquo;, &ldquo;आप&rdquo; और &ldquo;आपका&rdquo; आपको संदर्भित करता है, व्यक्ति इस वेबसाइट पर लॉग इन करता है और कंपनी के नियमों और शर्तों का अनुपालन करता है। &ldquo;कंपनी&rdquo;, &ldquo;हमारा&rdquo;, &ldquo;हम&rdquo;, &ldquo;हमारा&rdquo; और &ldquo;हमें&rdquo;, हमारी कंपनी को संदर्भित करता है। &ldquo;पार्टी&rdquo;, &ldquo;पार्टियां&rdquo;, या &ldquo;हमें&rdquo;, क्लाइंट और खुद दोनों को संदर्भित करता है। सभी शर्तें ग्राहक को हमारी सहायता की प्रक्रिया शुरू करने के लिए आवश्यक भुगतान की पेशकश, स्वीकृति और विचार का संदर्भ देती हैं, जो कंपनी की बताई गई सेवाओं के प्रावधान के संबंध में ग्राहक की जरूरतों को पूरा करने के व्यक्त उद्देश्य के लिए सबसे उपयुक्त तरीके से है। और नीदरलैंड के प्रचलित कानून के अधीन। उपरोक्त शब्दावली या अन्य शब्दों का एकवचन, बहुवचन, कैपिटलाइज़ेशन और/या वह/वह या वे में कोई भी उपयोग, विनिमेय के रूप में लिया जाता है और इसलिए इसका संदर्भ दिया जाता है।
</p>
<h3>
कुकीज़
</h3>
<p>
हम कुकीज़ के उपयोग को नियोजित करते हैं। वेबसाइट का नाम एक्सेस करके, आपने कंपनी के नाम की गोपनीयता नीति के साथ सहमति में कुकीज़ का उपयोग करने के लिए सहमति व्यक्त की है।
</p>
<p>
अधिकांश सहभागी वेबसाइटें हमें प्रत्येक विज़िट के लिए उपयोगकर्ता के विवरण को पुनः प्राप्त करने के लिए कुकीज़ का उपयोग करती हैं। हमारी वेबसाइट पर आने वाले लोगों के लिए इसे आसान बनाने के लिए कुछ क्षेत्रों की कार्यक्षमता को सक्षम करने के लिए कुकीज़ का उपयोग हमारी वेबसाइट द्वारा किया जाता है। हमारे कुछ सहयोगी/विज्ञापन भागीदार भी कुकीज़ का उपयोग कर सकते हैं।
</p>
<h3>
लाइसेंस
</h3>
<p>
जब तक अन्यथा न कहा गया हो,&nbsp;कंपनी का नाम&nbsp;और/या उसके लाइसेंसकर्ता वेबसाइट नाम पर सभी सामग्री के लिए बौद्धिक संपदा अधिकारों के स्वामी हैं। सभी बौद्धिक संपदा अधिकार सुरक्षित हैं। आप इन नियमों और शर्तों में निर्धारित प्रतिबंधों के अधीन अपने निजी इस्तेमाल के लिए&nbsp;वेबसाइट के नाम&nbsp;से इस तक पहुंच सकते हैं।
</p>
<p>
आपको नहीं करना चाहिए:
</p>
<उल>
<li>
वेबसाइट के नाम से&nbsp;सामग्री पुनर्प्रकाशित करें
</li>
<li>
वेबसाइट नाम . से सामग्री बेचें, किराए पर लें या उप-लाइसेंस दें
</li>
<li>
वेबसाइट का नाम . से सामग्री को पुन: प्रस्तुत, डुप्लिकेट या कॉपी करें
</li>
<li>
वेबसाइट के नाम से&nbsp;सामग्री पुनर्वितरित करें
</li>
</ul>
<p>
यह समझौता यहां की तारीख से शुरू होगा।
</p>
<p>
इस वेबसाइट के हिस्से उपयोगकर्ताओं को वेबसाइट के कुछ क्षेत्रों में राय और जानकारी पोस्ट करने और आदान-प्रदान करने का अवसर प्रदान करते हैं। कंपनी का नाम वेबसाइट पर उनकी उपस्थिति से पहले टिप्पणियों को फ़िल्टर, संपादित, प्रकाशित या समीक्षा नहीं करता है। टिप्पणियाँ कंपनी के नाम, उसके एजेंटों और/या सहयोगियों के विचारों और विचारों को नहीं दर्शाती हैं। टिप्पणियाँ उस व्यक्ति के विचारों और विचारों को दर्शाती हैं जो अपने विचार और राय पोस्ट करते हैं। लागू कानूनों द्वारा अनुमत सीमा तक,&nbsp;कंपनी का नाम&nbsp;टिप्पणियों के लिए या किसी भी दायित्व, क्षति या व्यय के लिए उत्तरदायी नहीं होगा और/या किसी भी उपयोग और/या पोस्टिंग और/या उपस्थिति के परिणामस्वरूप हुआ। इस वेबसाइट पर टिप्पणियाँ।
</p>
<p>
कंपनी का नाम&nbsp;सभी टिप्पणियों की निगरानी करने और किसी भी टिप्पणी को हटाने का अधिकार सुरक्षित रखता है जिसे अनुचित, आक्रामक माना जा सकता है या इन नियमों और शर्तों के उल्लंघन का कारण बनता है।
</p>
<p>
आप वारंट करते हैं और इसका प्रतिनिधित्व करते हैं:
</p>
<उल>
<li>
आप हमारी वेबसाइट पर टिप्पणियां पोस्ट करने के हकदार हैं और ऐसा करने के लिए आपके पास सभी आवश्यक लाइसेंस और सहमति हैं;
</li>
<li>
टिप्पणियाँ किसी भी बौद्धिक संपदा अधिकार पर आक्रमण नहीं करती हैं, जिसमें बिना किसी सीमा के कॉपीराइट, पेटेंट या किसी तीसरे पक्ष के ट्रेडमार्क शामिल हैं;
</li>
<li>
टिप्पणियों में कोई भी मानहानिकारक, अपमानजनक, आपत्तिजनक, अश्लील या अन्यथा गैरकानूनी सामग्री शामिल नहीं है जो गोपनीयता का आक्रमण है
</li>
<li>
टिप्पणियों का उपयोग व्यापार या प्रथा को बढ़ावा देने या व्यावसायिक गतिविधियों या गैरकानूनी गतिविधि को पेश करने के लिए नहीं किया जाएगा।
</li>
</ul>
<p>
आप एतद्द्वारा कंपनी का नाम&nbsp;किसी भी और सभी रूपों, प्रारूपों या मीडिया में अपनी किसी भी टिप्पणी का उपयोग, पुनरुत्पादन और संपादित करने के लिए दूसरों को उपयोग, पुन: पेश करने, संपादित करने और अधिकृत करने के लिए एक गैर-अनन्य लाइसेंस प्रदान करते हैं।
</p>
<h3>
हमारी सामग्री के लिए हाइपरलिंकिंग
</h3>
<p>
निम्नलिखित संगठन बिना पूर्व लिखित स्वीकृति के हमारी वेबसाइट से जुड़ सकते हैं:
</p>
<उल>
<li>
सरकारी संस्थाएं;
</li>
<li>
खोज यन्त्र;
</li>
<li>
समाचार संगठन;
</li>
<li>
ऑनलाइन निर्देशिका वितरक हमारी वेबसाइट से उसी तरह लिंक कर सकते हैं जैसे वे अन्य सूचीबद्ध व्यवसायों की वेबसाइटों से हाइपरलिंक करते हैं; तथा
</li>
<li>
गैर-लाभकारी संगठनों, चैरिटी शॉपिंग मॉल, और चैरिटी फंडरेज़िंग समूहों को छोड़कर जो हमारी वेब साइट से हाइपरलिंक नहीं हो सकते हैं, सिस्टम वाइड मान्यता प्राप्त व्यवसाय।
</li>
</ul>
<p>ये संगठन कहां से जुड़ सकते हैं</p>"
            ,'link_relation' => NULL,'target' => NULL,'active_key' => 'test','meta_title' => 'Terms and Conditions','meta_keywords' => 'Terms and Conditions','meta_description' => 'Terms and Conditions','meta_robots' => 'index, follow','meta_canonical' => NULL,'created_at' => '2020-06-30 12:40:36','updated_at' => '2020-07-02 13:37:37','deleted_at' => NULL),
            array('page_id' => '2','lang_id' => '2','name' => 'Privacy Policy','title' => 'Privacy Policy','description' => NULL,
                'page_content' => "
                <h2>गोपनीयता नीति</h2><p>आपकी निजता हमारे लिए महत्वपूर्ण है। हमारी वेबसाइट, https://www.hashtagcms.org, और हमारे स्वामित्व और संचालन वाली अन्य साइटों पर हम आपसे एकत्र की जा सकने वाली किसी भी जानकारी के संबंध में आपकी गोपनीयता का सम्मान करना Xyz साइट की नीति है।</p><p>हम व्यक्तिगत जानकारी केवल तभी मांगते हैं जब हमें आपको सेवा प्रदान करने के लिए वास्तव में इसकी आवश्यकता होती है। हम इसे आपके ज्ञान और सहमति से उचित और वैध तरीके से एकत्र करते हैं। हम आपको यह भी बताते हैं कि हम इसे क्यों एकत्र कर रहे हैं और इसका उपयोग कैसे किया जाएगा।</p><p>हम केवल तब तक एकत्रित जानकारी रखते हैं जब तक आपको आपकी अनुरोधित सेवा प्रदान करने के लिए आवश्यक हो। हम जो डेटा संग्रहीत करते हैं, हम हानि और चोरी को रोकने के साथ-साथ अनधिकृत पहुंच, प्रकटीकरण, प्रतिलिपि बनाने, उपयोग या संशोधन को रोकने के लिए व्यावसायिक रूप से स्वीकार्य साधनों के भीतर सुरक्षित रखेंगे।</p><p> हम सार्वजनिक रूप से या तीसरे पक्ष के साथ व्यक्तिगत रूप से पहचान करने वाली कोई भी जानकारी साझा नहीं करते हैं, जब तक कि कानून द्वारा आवश्यक न हो।</p><p> हमारी वेबसाइट बाहरी साइटों से लिंक हो सकती है जो हमारे द्वारा संचालित नहीं हैं। कृपया ध्यान रखें कि इन साइटों की सामग्री और प्रथाओं पर हमारा कोई नियंत्रण नहीं है, और हम उनकी संबंधित गोपनीयता नीतियों के लिए जिम्मेदारी या दायित्व स्वीकार नहीं कर सकते हैं।</p><p> आप अपनी व्यक्तिगत जानकारी के लिए हमारे अनुरोध को अस्वीकार करने के लिए स्वतंत्र हैं, इस समझ के साथ कि हम आपको आपकी कुछ वांछित सेवाएं प्रदान करने में असमर्थ हो सकते हैं।</p><p> हमारी वेबसाइट के आपके निरंतर उपयोग को गोपनीयता और व्यक्तिगत जानकारी के आसपास की हमारी प्रथाओं की स्वीकृति के रूप में माना जाएगा। यदि आपके कोई प्रश्न हैं कि हम उपयोगकर्ता डेटा और व्यक्तिगत जानकारी को कैसे संभालते हैं, तो बेझिझक हमसे संपर्क करें।</p><p> यह नीति 2 जुलाई 2020 से प्रभावी है।</p>
                ",

                'link_relation' => NULL,'target' => NULL,'active_key' => 'privacy-policy','meta_title' => NULL,'meta_keywords' => NULL,'meta_description' => NULL,'meta_robots' => NULL,'meta_canonical' => NULL,'created_at' => '2020-07-02 10:37:07','updated_at' => '2020-07-05 09:25:25','deleted_at' => NULL),
            array('page_id' => '3','lang_id' => '2','name' => 'टेस्ट ब्लॉग ','title' => 'टेस्ट ब्लॉग','description' => 'यह एक परीक्षण ब्लॉग है ','page_content' => 'यह परीक्षण ब्लॉग सामग्री है। ','link_relation' => NULL,'target' => NULL,'active_key' => 'test-blog','meta_title' => NULL,'meta_keywords' => NULL,'meta_description' => NULL,'meta_robots' => NULL,'meta_canonical' => NULL,'created_at' => '2020-07-11 10:47:26','updated_at' => '2020-07-11 10:47:26','deleted_at' => NULL)
        );


        if(DB::table($table_name)->get()->count() == 0) {
            DB::table($table_name)->insert($pages);
            DB::table($table_name_langs)->insert($page_langs);
            DB::table($table_name_langs)->insert($page_langs_hi);
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }
    }
}
