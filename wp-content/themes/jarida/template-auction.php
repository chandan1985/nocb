<?php
/*
  Template Name: Auction
 */
?>
<link rel="stylesheet" type="text/css" href="/wp-content/plugins/tdc-paywall-psa/style.css">
<script type="text/javascript" src="/wp-includes/js/jquery/jquery.js"></script>
<script type="text/javascript" src="/wp-content/plugins/tdc-paywall-psa/login_form.js"></script>
<style type="text/css">
a[href] {
    white-space:normal;
}
.dmcss_login_form #form_nav { height: 44px !important;}

/* Client-specific Styles */
#outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;}
/* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
.ExternalClass {width:100%;} /* Force Hotmail to display emails at full width */
.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing.  More on that: http://www.emailonacid.com/forum/viewthread/43/ */
#backgroundTable {margin:0; padding:0; width:100% !important;}
img {outline:none; text-decoration:none;border:none; -ms-interpolation-mode: bicubic;}
a img {border:none;}
.image_fix {display:block;}

table td {border-collapse: collapse;}
table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
/*a {color: #e95353;text-decoration: none;text-decoration:none!important;}*/
/*STYLES*/
table[class=full] { width: 100%; clear: both; }

/*################################################*/
/*tablet STYLES*/
/*################################################*/
@media only screen and (max-width: 640px) {
a[href^="tel"], a[href^="sms"] {
text-decoration: default;
color: inherit;
pointer-events: none;
cursor: default;
}
.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
text-decoration: default;
color: #ffffff !important;
pointer-events: auto;
cursor: default;
}
td[class=devicewidth] img                   {max-width: 432px!important; height: auto!important;}
td[class=devicewidthouter] img              {max-width: 432px!important; height: auto!important;}
td[class=devicewidth] img:not([width])      {width: auto!important;}
td[class=devicewidthouter] img:not([width]) {width: auto!important;}
table[class=devicewidth]                    {width: 432px!important;}
div[class=devicewidth]                      {width: 432px!important;}
td[class=devicewidth]                       {width: 432px!important; display: table!important;}
td[class=devicewidthtable]                  {width: 432px!important;}
img[class=devicewidth]                      {width: 432px!important;}
table[class=devicewidthouter]               {width: 440px!important;}
div[class=devicewidthouter]                 {width: 440px!important;}
td[class=devicewidthouter]                  {width: 440px!important;}
img[class=devicewidthouter]                 {width: 440px!important;}
table[class=devicewidthborder]              {width: 422px!important;}
div[class=devicewidthborder]                {width: 422px!important;}
td[class=devicewidthborder]                 {width: 422px!important;}
img[class=devicewidthborder]                {width: 422px!important;}
td[class=milogo] img                        {max-width: 432px!important; height:auto!important;}
td[class=mologo] img                        {max-width: 440px!important; height:auto!important;}
td[class=miheader] img                      {max-width: 432px!important; height:auto!important;}
td[class=moheader] img                      {max-width: 440px!important; height:auto!important;}
td[class=mifooter] img                      {max-width: 432px!important; height:auto!important;}
td[class=mofooter] img                      {max-width: 440px!important; height:auto!important;}
td[class=miheader], td[class=miheader] table    {width: 432px!important;}
td[class=moheader], td[class=moheader] table    {width: 440px!important;}
td[class=mifooter], td[class=mifooter] table    {width: 432px!important;}
td[class=mofooter], td[class=mofooter] table    {width: 440px!important;}
}

/*##############################################*/
/*PHONE STYLES  IPHONE 6 Plus*/
/*##############################################*/
@media only screen and (max-width: 440px) {
a[href^="tel"], a[href^="sms"] {
text-decoration: default;
color: inherit;
pointer-events: none;
cursor: default;
}
.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
text-decoration: default;
color: #ffffff !important;
pointer-events: auto;
cursor: default;
}
td[class=devicewidth] img                   {max-width: 406px!important; height: auto!important;}
td[class=devicewidthouter] img              {max-width: 406px!important; height: auto!important;}
td[class=devicewidth] img:not([width])      {width: auto!important;}
td[class=devicewidthouter] img:not([width]) {width: auto!important;}
table[class=devicewidth]                    {width: 406px!important;}
div[class=devicewidth]                      {width: 406px!important;}
td[class=devicewidth]                       {width: 406px!important; display: table!important;}
td[class=devicewidthtable]                  {width: 406px!important;}
img[class=devicewidth]                      {width: 406px!important;}
table[class=devicewidthouter]               {width: 414px!important;}
div[class=devicewidthouter]                 {width: 414px!important;}
td[class=devicewidthouter]                  {width: 414px!important;}
img[class=devicewidthouter]                 {width: 414px!important;}
table[class=devicewidthborder]              {width: 396px!important;}
div[class=devicewidthborder]                {width: 396px!important;}
td[class=devicewidthborder]                 {width: 396px!important;}
img[class=devicewidthborder]                {width: 396px!important;}
td[class=milogo] img                        {max-width: 406px!important; height:auto!important;}
td[class=mologo] img                        {max-width: 414px!important; height:auto!important;}
td[class=miheader] img                      {max-width: 406px!important; height:auto!important;}
td[class=moheader] img                      {max-width: 414px!important; height:auto!important;}
td[class=mifooter] img                      {max-width: 406px!important; height:auto!important;}
td[class=mofooter] img                      {max-width: 414px!important; height:auto!important;}
td[class=miheader], td[class=miheader] table    {width: 406px!important;}
td[class=moheader], td[class=moheader] table    {width: 414px!important;}
td[class=mifooter], td[class=mifooter] table    {width: 406px!important;}
td[class=mofooter], td[class=mofooter] table    {width: 414px!important;}
}

/*##############################################*/
/*PHONE STYLES IPHONE 6*/
/*##############################################*/
@media only screen and (max-width: 375px) {
a[href^="tel"], a[href^="sms"] {
text-decoration: default;
color: inherit;
pointer-events: none;
cursor: default;
}
.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
text-decoration: default;
color: #ffffff !important;
pointer-events: auto;
cursor: default;
}
td[class=devicewidth] img                   {max-width: 367px!important; height: auto!important;}
td[class=devicewidthouter] img              {max-width: 367px!important; height: auto!important;}
td[class=devicewidth] img:not([width])      {width: auto!important;}
td[class=devicewidthouter] img:not([width]) {width: auto!important;}
table[class=devicewidth]                    {width: 367px!important;}
div[class=devicewidth]                      {width: 367px!important;}
td[class=devicewidth]                       {width: 367px!important; display: table!important;}
td[class=devicewidthtable]                  {width: 367px!important;}
img[class=devicewidth]                      {width: 367px!important;}
table[class=devicewidthouter]               {width: 375px!important;}
div[class=devicewidthouter]                 {width: 375px!important;}
td[class=devicewidthouter]                  {width: 375px!important;}
img[class=devicewidthouter]                 {width: 375px!important;}
table[class=devicewidthborder]              {width: 357px!important;}
div[class=devicewidthborder]                {width: 357px!important;}
td[class=devicewidthborder]                 {width: 357px!important;}
img[class=devicewidthborder]                {width: 357px!important;}
td[class=milogo] img                        {max-width: 367px!important; height:auto!important;}
td[class=mologo] img                        {max-width: 375px!important; height:auto!important;}
td[class=miheader] img                      {max-width: 367px!important; height:auto!important;}
td[class=moheader] img                      {max-width: 375px!important; height:auto!important;}
td[class=mifooter] img                      {max-width: 367px!important; height:auto!important;}
td[class=mofooter] img                      {max-width: 375px!important; height:auto!important;}
td[class=miheader], td[class=miheader] table    {width: 367px!important;}
td[class=moheader], td[class=moheader] table    {width: 375px!important;}
td[class=mifooter], td[class=mifooter] table    {width: 367px!important;}
td[class=mofooter], td[class=mofooter] table    {width: 375px!important;}
}

/*##############################################*/
/*PHONE STYLES*/
/*##############################################*/
@media only screen and (max-width: 374px) {
a[href^="tel"], a[href^="sms"] {
text-decoration: default;
color: inherit;
pointer-events: none;
cursor: default;
}
.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
text-decoration: default;
color: #ffffff !important;
pointer-events: auto;
cursor: default;
}
td[class=devicewidth] img                   {max-width: 272px!important; height: auto!important;}
td[class=devicewidthouter] img              {max-width: 272px!important; height: auto!important;}
td[class=devicewidth] img:not([width])      {width: auto!important;}
td[class=devicewidthouter] img:not([width]) {width: auto!important;}
table[class=devicewidth]                    {width: 272px!important;}
div[class=devicewidth]                      {width: 272px!important;}
td[class=devicewidth]                       {width: 272px!important; display: table!important;}
td[class=devicewidthtable]                  {width: 272px!important;}
img[class=devicewidth]                      {width: 272px!important;}
table[class=devicewidthouter]               {width: 280px!important;}
div[class=devicewidthouter]                 {width: 280px!important;}
td[class=devicewidthouter]                  {width: 280px!important;}
img[class=devicewidthouter]                 {width: 280px!important;}
table[class=devicewidthborder]              {width: 262px!important;}
div[class=devicewidthborder]                {width: 262px!important;}
td[class=devicewidthborder]                 {width: 262px!important;}
img[class=devicewidthborder]                {width: 262px!important;}
td[class=milogo] img                        {max-width: 272px!important; height:auto!important;}
td[class=mologo] img                        {max-width: 280px!important; height:auto!important;}
td[class=miheader] img                      {max-width: 272px!important; height:auto!important;}
td[class=moheader] img                      {max-width: 280px!important; height:auto!important;}
td[class=mifooter] img                      {max-width: 272px!important; height:auto!important;}
td[class=mofooter] img                      {max-width: 280px!important; height:auto!important;}
td[class=miheader], td[class=miheader] table    {width: 272px!important;}
td[class=moheader], td[class=moheader] table    {width: 280px!important;}
td[class=mifooter], td[class=mifooter] table    {width: 272px!important;}
td[class=mofooter], td[class=mofooter] table    {width: 280px!important;}
}

@media only screen and (max-width: 640px) {
td[class=devicewidth] .mcol1 { width: 392px!important; }
td[class=devicewidth] .mcol1 img { max-width: 392px!important;height: auto!important; }
td[class=devicewidth] .mcol2 { width: 392px!important; }
td[class=devicewidth] .mcol2 img { max-width: 392px!important;height: auto!important; }
td[class=devicewidth] .mcol3 { width: 432px!important; }
td[class=devicewidth] .mcol3 img { max-width: 432px!important;height: auto!important; }
td[class=devicewidth] .mcol4 { width: 424px!important; }
td[class=devicewidth] .mcol4 img { max-width: 424px!important;height: auto!important; }
td[class=devicewidth] .mcol5 { width: 432px!important; }
td[class=devicewidth] .mcol5 img { max-width: 432px!important;height: auto!important; }
td[class=devicewidth] .mcol6 { width: 424px!important; }
td[class=devicewidth] .mcol6 img { max-width: 424px!important;height: auto!important; }
td[class=devicewidth] .mcol7 { width: 432px!important; }
td[class=devicewidth] .mcol7 img { max-width: 432px!important;height: auto!important; }
td[class=devicewidth] .mcol8 { width: 424px!important; }
td[class=devicewidth] .mcol8 img { max-width: 424px!important;height: auto!important; }
}
@media only screen and (max-width: 440px) {
td[class=devicewidth] .mcol1 { width: 366px!important; }
td[class=devicewidth] .mcol1 img { max-width: 366px!important;height: auto!important; }
td[class=devicewidth] .mcol2 { width: 366px!important; }
td[class=devicewidth] .mcol2 img { max-width: 366px!important;height: auto!important; }
td[class=devicewidth] .mcol3 { width: 406px!important; }
td[class=devicewidth] .mcol3 img { max-width: 406px!important;height: auto!important; }
td[class=devicewidth] .mcol4 { width: 398px!important; }
td[class=devicewidth] .mcol4 img { max-width: 398px!important;height: auto!important; }
td[class=devicewidth] .mcol5 { width: 406px!important; }
td[class=devicewidth] .mcol5 img { max-width: 406px!important;height: auto!important; }
td[class=devicewidth] .mcol6 { width: 398px!important; }
td[class=devicewidth] .mcol6 img { max-width: 398px!important;height: auto!important; }
td[class=devicewidth] .mcol7 { width: 406px!important; }
td[class=devicewidth] .mcol7 img { max-width: 406px!important;height: auto!important; }
td[class=devicewidth] .mcol8 { width: 398px!important; }
td[class=devicewidth] .mcol8 img { max-width: 398px!important;height: auto!important; }
}
@media only screen and (max-width: 375px) {
td[class=devicewidth] .mcol1 { width: 327px!important; }
td[class=devicewidth] .mcol1 img { max-width: 327px!important;height: auto!important; }
td[class=devicewidth] .mcol2 { width: 327px!important; }
td[class=devicewidth] .mcol2 img { max-width: 327px!important;height: auto!important; }
td[class=devicewidth] .mcol3 { width: 367px!important; }
td[class=devicewidth] .mcol3 img { max-width: 367px!important;height: auto!important; }
td[class=devicewidth] .mcol4 { width: 359px!important; }
td[class=devicewidth] .mcol4 img { max-width: 359px!important;height: auto!important; }
td[class=devicewidth] .mcol5 { width: 367px!important; }
td[class=devicewidth] .mcol5 img { max-width: 367px!important;height: auto!important; }
td[class=devicewidth] .mcol6 { width: 359px!important; }
td[class=devicewidth] .mcol6 img { max-width: 359px!important;height: auto!important; }
td[class=devicewidth] .mcol7 { width: 367px!important; }
td[class=devicewidth] .mcol7 img { max-width: 367px!important;height: auto!important; }
td[class=devicewidth] .mcol8 { width: 359px!important; }
td[class=devicewidth] .mcol8 img { max-width: 359px!important;height: auto!important; }
}
@media only screen and (max-width: 374px) {
td[class=devicewidth] .mcol1 { width: 232px!important; }
td[class=devicewidth] .mcol1 img { max-width: 232px!important;height: auto!important; }
td[class=devicewidth] .mcol2 { width: 232px!important; }
td[class=devicewidth] .mcol2 img { max-width: 232px!important;height: auto!important; }
td[class=devicewidth] .mcol3 { width: 272px!important; }
td[class=devicewidth] .mcol3 img { max-width: 272px!important;height: auto!important; }
td[class=devicewidth] .mcol4 { width: 264px!important; }
td[class=devicewidth] .mcol4 img { max-width: 264px!important;height: auto!important; }
td[class=devicewidth] .mcol5 { width: 272px!important; }
td[class=devicewidth] .mcol5 img { max-width: 272px!important;height: auto!important; }
td[class=devicewidth] .mcol6 { width: 264px!important; }
td[class=devicewidth] .mcol6 img { max-width: 264px!important;height: auto!important; }
td[class=devicewidth] .mcol7 { width: 272px!important; }
td[class=devicewidth] .mcol7 img { max-width: 272px!important;height: auto!important; }
td[class=devicewidth] .mcol8 { width: 264px!important; }
td[class=devicewidth] .mcol8 img { max-width: 264px!important;height: auto!important; }
}
</style>

<style type="text/css">
      body, table, tr, td, div, textarea, input
        {
            color:                  #000000;
            font-family:            'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size:              12px;

        }
    a[href^="x-apple-data-detectors:"], a[href^="tel:"] {
        color: inherit;
        font-size: inherit;
        font-family: inherit;
        text-decoration: inherit;
    }
</style>
<div style="background-color: #aaaaaa; ">
    <table border="0" width="100%" height="100%" cellspacing="0" cellpadding="0" id="backgroundTable" style="width: 100%; height: 100%;"><tr><td align="center" valign="top" style="width: 100%; height: 100%;">
        <table width="608" border="0" cellspacing="0" cellpadding="0" class="devicewidthouter">

                <div style="display: none; font-size: 1px; color: #aaaaaa; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
                    A list of upcoming foreclosure auctions in Hennepin and Ramsey counties.
                </div>
                 <tr><td width="100%" bgcolor=transparent style="padding: 0 0 0 0 ; text-align: center;" align="HEADER-ALIGNMENT" class="moheader">



            </td></tr>
        </table>
        
        <table width="608" border="0" cellspacing="0" cellpadding="0" class="devicewidthouter">
            <tr>
                <td width="4" style="font-size: 0; line-height: 0;"><img src="//news.finance-commerce.com/cdnr/50/acton/imgs/t/4/1/t.gif" border="0" width="4" height="1"></td>
                <td width="5" style="font-size: 0; line-height: 0;"><img src="//news.finance-commerce.com/cdnr/50/acton/imgs/t/5/1/t.gif" border="0" width="5" height="1"></td>
                <td width="590" class="devicewidthborder" style="font-size: 0; line-height: 0;"><img class="devicewidthborder" src="//news.finance-commerce.com/cdnr/50/acton/imgs/t/590/1/t.gif" border="0" width="590" height="1"></td>
                <td width="5" style="font-size: 0; line-height: 0;"><img src="//news.finance-commerce.com/cdnr/50/acton/imgs/t/5/1/t.gif" border="0" width="5" height="1"></td>
                <td width="4" style="font-size: 0; line-height: 0;"><img src="//news.finance-commerce.com/cdnr/50/acton/imgs/t/4/1/t.gif" border="0" width="4" height="1"></td>
            </tr>
            <tr><td width="9" height="9" rowspan="2" colspan="2" style="font-size: 1px; line-height: 1px; background-size: 100%;" background="https://news.finance-commerce.com/cdnr/50/acton/imgs/rc/4/9/aaaaaa/FFFFFF/FFFFFF/tl/c.png" ><!--[if mso]><img border="0" width="9" height="9" src="//news.finance-commerce.com/cdnr/50/acton/imgs/rc/4/9/aaaaaa/FFFFFF/FFFFFF/tl/c.png" ><![endif]--><!--[if !mso]><!--><img border="0" width="9" height="1" src="//news.finance-commerce.com/cdnr/50/acton/imgs/t.gif" ><!--<![endif]--></td><!-- nospace
                 --><td width="590" height="4" bgcolor="#FFFFFF" class="devicewidthborder" style="font-size: 0; line-height: 0;" valign="top"><img src="//news.finance-commerce.com/cdnr/50/acton/imgs/t/590/1/t.gif" border="0" width="590" height="1" class="devicewidthborder"></td><!-- no space
                 --><td width="9" height="9" rowspan="2" colspan="2" style="font-size: 1px; line-height: 1px; background-size: 100%;" background="https://news.finance-commerce.com/cdnr/50/acton/imgs/rc/4/9/aaaaaa/FFFFFF/FFFFFF/tr/c.png" ><!--[if mso]><img border="0" width="9" height="9" src="//news.finance-commerce.com/cdnr/50/acton/imgs/rc/4/9/aaaaaa/FFFFFF/FFFFFF/tr/c.png" ><![endif]--><!--[if !mso]><!--><img border="0" width="9" height="1" src="//news.finance-commerce.com/cdnr/50/acton/imgs/t.gif" ><!--<![endif]--></td>
            </tr>
            <tr><td width="590" height="5" bgcolor="#FFFFFF" class="devicewidthborder" style="font-size: 0; line-height: 0;" valign="top"><img src="//news.finance-commerce.com/cdnr/50/acton/imgs/t/590/1/t.gif" border="0" width="590" height="1" class="devicewidthborder"></td></tr>
            <tr><td width="4" bgcolor="#FFFFFF" style="font-size: 0; line-height: 0;"><img src="//news.finance-commerce.com/cdnr/50/acton/imgs/t.gif" border="0" width="4" height="1"></td><!-- no space
                 --><td width="600" colspan="3" bgcolor="#FFFFFF" valign="top" align="left" class="devicewidthtable">

        <table width="600" border="0" cellspacing="0" cellpadding="0" class="devicewidth">
            <tr><td width="100%" bgcolor="#FFFFFF" valign="top" align="suppress" class="milogo" style="padding: 0; text-align: suppress;"></td></tr>
            <tr><td width="600" bgcolor="#FFFFFF" valign="top" align="left" class="devicewidthtable">

            
<table border="0" cellspacing="0" cellpadding="0" width="100%" class="devicewidth">
  <tr>
    <td width="100%">
      <table border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse;" width="600" class="devicewidth">
        <tr>
          <td valign="top" width="300" class="devicewidth" bgcolor="transparent" style="border-collapse: collapse; box-sizing: border-box; border-top: none; border-right: none; border-left: none; border-bottom: none;">
            <table cellspacing="0" cellpadding="0" width="100%" height="100%"  style="width: 100%; height: 100%; border-collapse: separate;">
              <tr>
                <td valign="top" class="mcol1" style="border: 0; border-collapse: separate; width: 100%; height: 100%; padding: 20px 20px 20px 20px;">
                  <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                      <td valign="top" style="padding-top:0px; padding-bottom:0px; padding-left:0px; padding-right:0px;"  id="B1484839891038" class="ao_PictureStripBlock">
                        <a name="B1484839891038"></a>
                        <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" height="auto" style="line-height: 150%; ">
                          <tbody>
                            <tr style="line-height: 150%; ">
                              <td style="line-height: 150%; ">
                                <table border="0" cellspacing="0" cellpadding="0" align="center" width="100%" style="line-height: 150%; table-layout: fixed;">
                                  <tbody>
                                    <tr style="line-height: 150%; ">
                                      <td style="font-size: 0; line-height: 100%;" align="center"><a target="_blank" style="color: #2880BB; text-decoration: underline; "
                                        id="ct1_0"
                                        href="<?php echo get_field( "finance_and_commerce_logo_url" ); ?>"><img border="0" width="260" style="max-width: 100% !important; height: auto; display:block !important;  font-size: 12px;" alt="Finance &amp; Commerce" title="Finance &amp; Commerce" src="<?php echo get_field( "finance_and_commerce_logo" ); ?>" height="26"></a></td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td valign="top" style="padding-top:0px; padding-bottom:0px; padding-left:0px; padding-right:0px;"  id="B1493912512171" class="ao_SpacerBlock">
                        <a name="B1493912512171"></a>        
                        <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%">
                          <tr>
                            <td style="padding:5px;font-size: 0px; line-height:0px;" width="100%">
                              <table border="0" cellspacing="0" cellpadding="0" height="5" width="100%" style="font-size: 0px; line-height: 0px">
                                <tr>
                                  <td height="5" width="100%" style="border-collapse:collapse;font-size:0px; line-height:0px;" bgcolor="#FFFFFF">
                                    <img src="//news.finance-commerce.com/acton/image/transpix.gif" alt="" height="1" width="1" />
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td valign="top" style="padding-top:0px; padding-bottom:0px; padding-left:0px; padding-right:0px;"  id="B1493844946199" class="ao_PictureStripBlock">
                        <a name="B1493844946199"></a>
                        <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" height="auto" style="line-height: 150%; ">
                          <tbody>
                            <tr style="line-height: 150%; ">
                              <td style="line-height: 150%; ">
                                <table border="0" cellspacing="0" cellpadding="0" align="center" width="100%" style="line-height: 150%; table-layout: fixed;">
                                  <tbody>
                                    <tr style="line-height: 150%; ">
                                      <td style="font-size: 0; line-height: 100%;" align="center"><a target="_blank" style="color: #2880BB; text-decoration: underline; "
                                        id="ct2_0"
                                        href="<?php echo get_field( "minnesota_logo_url" ); ?>"><img border="0" width="260" style="max-width: 100% !important; height: auto; display:block !important;  font-size: 12px;" alt="Minnesota Lawyer" title="Minnesota Lawyer" src="<?php echo get_field( "minnesota_logo" ); ?>" height="39"></a></td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
          <td valign="top" width="300" class="devicewidth" bgcolor="transparent" style="border-collapse: collapse; box-sizing: border-box; border-top: none; border-right: none; border-left: none; border-bottom: none;"
            >
            <table cellspacing="0" cellpadding="0" width="100%" height="100%"
              style="width: 100%; height: 100%; border-collapse: separate;"
              >
              <tr>
                <td valign="top" class="mcol2" style="border: 0; border-collapse: separate; width: 100%; height: 100%; padding: 10px 20px 20px 20px;"
                  >
                  <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                      <td valign="top" style="padding-top:0px; padding-bottom:0px; padding-left:0px; padding-right:0px;"  id="B1484839959437" class="ao_FreeTextBlock">
                        <a name="B1484839959437"></a>
                        <h1><span style="line-height: 150%; font-size: 18pt;">Auction Notices<br>At a Glance<br></span></h1>
                        <p style="line-height: 150%; ">&nbsp;</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td width="100%">
      <table border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse;" width="600" class="devicewidth">
        <tr>
          <td valign="top" width="600" class="devicewidth" bgcolor="transparent" style="border-collapse: collapse; box-sizing: border-box; border-top: none; border-right: none; border-left: none; border-bottom: none;"
            >
            <table cellspacing="0" cellpadding="0" width="100%" height="100%"
              style="width: 100%; height: 100%; border-collapse: separate;"
              >
              <tr>
                <td valign="top" class="mcol1" style="border: 0; border-collapse: separate; width: 100%; height: 100%; "
                  >
                  <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                      <td valign="top" style="padding-top:0px; padding-bottom:0px; padding-left:0px; padding-right:0px;"  id="B1492023722394" class="ao_PictureStripBlock">
                        <a name="B1492023722394"></a>
                        <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" height="auto" style="line-height: 150%; ">
                          <tbody>
                            <tr style="line-height: 150%; ">
                              <td style="line-height: 150%; ">
                                <table border="0" cellspacing="0" cellpadding="0" align="center" width="100%" style="line-height: 150%; table-layout: fixed;">
                                  <tbody>
                                    <tr style="line-height: 150%; ">
                                      <td style="font-size: 0; line-height: 100%;  " align="center"><img border="0" width="450" style="max-width: 100% !important; height: auto; display:block !important; font-size: 12px;" alt="" title="" src="<?php echo get_field( "middle_image" ); ?>" height="160"></td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td width="100%">
      <table border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse;" width="600" class="devicewidth">
        <tr>
          <td valign="top" width="600" class="devicewidth" bgcolor="transparent" style="border-collapse: collapse; box-sizing: border-box; border-top: none; border-right: none; border-left: none; border-bottom: none;"
            >
            <table cellspacing="0" cellpadding="0" width="100%" height="100%"
              style="width: 100%; height: 100%; border-collapse: separate;"
              >
              <tr>
                <td valign="top" class="mcol1" style="border: 0; border-collapse: separate; width: 100%; height: 100%; padding: 4px 4px 4px 4px;"
                  >
                  <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                      <td valign="top" style="padding-top:0px; padding-bottom:0px; padding-left:0px; padding-right:0px;"  id="B1491938898723" class="ao_SpacerBlock">
                        <a name="B1491938898723"></a>
                        <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%">
                          <tr>
                            <td style="padding:5px;font-size: 0px; line-height:0px;" width="100%">
                              <table border="0" cellspacing="0" cellpadding="0" height="4" width="100%" style="font-size: 0px; line-height: 0px">
                                <tr>
                                  <td height="4" width="100%" style="border-collapse:collapse;font-size:0px; line-height:0px;" bgcolor="#065D92">
                                    <img src="//news.finance-commerce.com/acton/image/transpix.gif" alt="" height="1" width="1" />
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                   <?php echo get_field('auction_header'); ?>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>

<?php 

    //$week = date("W");

    $pdf_query = new WP_Query( // Start a new query for our videos
    array(
        'post_type' => 'attachment', // Only bring back attachments
        'posts_per_page' => '1', // Show us the first result
        'post_mime_type' => 'application/pdf',
        'post_status' => 'inherit', // Attachments default to "inherit", rather than published. Use "inherit" or "all". 
        // 'date_query' => array('week' => $week), // choosing date range
        'order_by' => 'date',
        'order'  => 'DESC',
        )
    );  


    $excel_query = new WP_Query( // Start a new query for our videos
    array(
        'post_type' => 'attachment', // Only bring back attachments
        'posts_per_page' => '1', // Show us the first result
        'post_mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel',
        'post_status' => 'inherit', // Attachments default to "inherit", rather than published. Use "inherit" or "all". 
        // 'date_query' => array('week' => $week), // choosing date range
        'order_by' => 'date',
        'order'  => 'DESC',
        )
    );  



  if ( is_user_logged_in() ) { 
  foreach ($excel_query->posts as $key => $value) {

  if($value->post_mime_type == 'application/vnd.ms-excel' || $value->post_mime_type  == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') { ?>
  <tr>
    <td width="100%">
      <table border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse;" width="600" class="devicewidth">
        <tr>
          <td valign="top" width="600" class="devicewidth" bgcolor="transparent" style="border-collapse: collapse; box-sizing: border-box; border-top: none; border-right: none; border-left: none; border-bottom: none;"
            >
            <table cellspacing="0" cellpadding="0" width="100%" height="100%"
              style="width: 100%; height: 100%; border-collapse: separate;"
              >
              <tr>
                <td valign="top" class="mcol1" style="border: 0; border-collapse: separate; width: 100%; height: 100%; "
                  >
                  <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                      <td valign="top" style="padding-top:0px; padding-bottom:0px; padding-left:0px; padding-right:0px;"  id="B1493912871674" class="ao_SpacerBlock">
                        <a name="B1493912871674"></a>
                        <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%">
                          <tr>
                            <td style="padding:5px;font-size: 0px; line-height:0px;" width="100%">
                              <table border="0" cellspacing="0" cellpadding="0" height="4" width="100%" style="font-size: 0px; line-height: 0px">
                                <tr>
                                  <td height="4" width="100%" style="border-collapse:collapse;font-size:0px; line-height:0px;" bgcolor="#065D92">
                                    <img src="//news.finance-commerce.com/acton/image/transpix.gif" alt="" height="1" width="1" />
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  
<tr>
    <td width="100%">
      <table border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse;" width="600" class="devicewidth">
        <tr>
          <td valign="top" width="600" class="devicewidth" bgcolor="transparent" style="border-collapse: collapse; box-sizing: border-box; border-top: none; border-right: none; border-left: none; border-bottom: none;"
            >
            <table cellspacing="0" cellpadding="0" width="100%" height="100%"
              style="width: 100%; height: 100%; border-collapse: separate;"
              >
              <tr>
                <td valign="top" class="mcol1" style="border: 0; border-collapse: separate; width: 100%; height: 100%; padding: 4px 4px 4px 4px;"
                  >
                  <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                      <td valign="top" style="padding-top:0px; padding-bottom:0px; padding-left:0px; padding-right:0px;"  id="B1484840973089" class="ao_FreeTextBlock">
                        <a name="B1484840973089"></a>
                        <h2>Download Spreadsheet</h2>
                        <p style="line-height: 150%; ">The spreadsheet can be sorted in Excel or other spreadsheet software. Each listing includes a web link to the same listing on our public notice website, which you can use to see the property on Google Maps, details about the property including lot size and square footage, and other information. Hennepin County notices have already been published <a style="color: #2880BB; text-decoration: underline; line-height: 150%; "
                          id="ct3_0"
                          href="https://news.finance-commerce.com/acton/ct/22719/s-09fc-1812/Bct/q-0085/l-007f:52a3/ct3_0/1?sid=TV2%3AslEaxYkT3">in Finance &amp; Commerce</a> and Ramsey County notices have been published <a style="color: #2880BB; text-decoration: underline; line-height: 150%; "
                          id="ct4_0"
                          href="https://news.finance-commerce.com/acton/ct/22719/s-09fc-1812/Bct/q-0085/l-007f:52a3/ct4_0/1?sid=TV2%3AslEaxYkT3">in Minnesota Lawyer</a>.</p>
                        <table class="mceNonEditable aoButtonTable" style="line-height: 150%; margin-top: 3px; margin-left: auto; margin-right: auto;" cellspacing="0" cellpadding="5">
                          <tbody>
                            <tr style="line-height: 150%; ">
                              <td class="aoButtonTable" style="line-height: 150%; padding: 10px 20px; text-align: center; background-color: #336699; color: #ffffff; border-radius: 6px; -webkit-border-radius: 6px; -moz-border-radius: 6px;">
                                <a class="aoButtonAnchor" style="line-height: 150%; word-break: normal; word-wrap: normal; text-decoration: none; color: #ffffff; font-size: 14px; font-weight: bold; font-family: Arial;" target="_blank" id="ct5_0" href="<?php echo esc_url( $value->guid ); ?>">
                                    Download .XLS
                                </a>
                            </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
 <?php } }
   foreach ($pdf_query->posts as $key => $value) { 
 	if($value->post_mime_type == 'application/pdf') {  ?>
<tr>
    <td width="100%">
      <table border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse;" width="600" class="devicewidth">
        <tr>
          <td valign="top" width="600" class="devicewidth" bgcolor="transparent" style="border-collapse: collapse; box-sizing: border-box; border-top: none; border-right: none; border-left: none; border-bottom: none;"
            >
            <table cellspacing="0" cellpadding="0" width="100%" height="100%"
              style="width: 100%; height: 100%; border-collapse: separate;"
              >
              <tr>
                <td valign="top" class="mcol1" style="border: 0; border-collapse: separate; width: 100%; height: 100%; "
                  >
                  <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                      <td valign="top" style="padding-top:0px; padding-bottom:0px; padding-left:0px; padding-right:0px;"  id="B1491939186754" class="ao_SpacerBlock">
                        <a name="B1491939186754"></a>
                        <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%">
                          <tr>
                            <td style="padding:5px;font-size: 0px; line-height:0px;" width="100%">
                              <table border="0" cellspacing="0" cellpadding="0" height="4" width="100%" style="font-size: 0px; line-height: 0px">
                                <tr>
                                  <td height="4" width="100%" style="border-collapse:collapse;font-size:0px; line-height:0px;" bgcolor="#065D92">
                                    <img src="//news.finance-commerce.com/acton/image/transpix.gif" alt="" height="1" width="1" />
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
<tr>
    <td width="100%">
      <table border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse;" width="600" class="devicewidth">
        <tr>
          <td valign="top" width="600" class="devicewidth" bgcolor="transparent" style="border-collapse: collapse; box-sizing: border-box; border-top: none; border-right: none; border-left: none; border-bottom: none;"
            >
            <table cellspacing="0" cellpadding="0" width="100%" height="100%"
              style="width: 100%; height: 100%; border-collapse: separate;"
              >
              <tr>
                <td valign="top" class="mcol1" style="border: 0; border-collapse: separate; width: 100%; height: 100%; padding: 4px 4px 4px 4px;"
                  >
                  <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                      <td valign="top" style="padding-top:0px; padding-bottom:0px; padding-left:0px; padding-right:0px;"  id="B1484840640892" class="ao_FreeTextBlock">
                        <a name="B1484840640892"></a>
                        <h2>Download PDF</h2>
                        <p style="line-height: 150%; ">You may use the button below to view a PDF of foreclosure auctions appearing in Finance &amp; Commerce and Minnesota Lawyer.</p>
                        <table class="mceNonEditable aoButtonTable" style="line-height: 150%; margin-top: 3px; margin-left: auto; margin-right: auto;" cellspacing="0" cellpadding="5">
                           <tbody>
                            <tr style="line-height: 150%; ">
                              <td class="aoButtonTable" style="line-height: 150%; padding: 10px 20px; text-align: center; background-color: #336699; color: #ffffff; border-radius: 6px; -webkit-border-radius: 6px; -moz-border-radius: 6px;"><a class="aoButtonAnchor" style="line-height: 150%; word-break: normal; word-wrap: normal; text-decoration: none; color: #ffffff; font-size: 14px; font-weight: bold; font-family: Arial;" target="_blank"
                                id="ct6_0"
                                href="<?php echo esc_url( $value->guid ); ?>">Download .PDF</a></td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
            <!--         <tr>
                      <td valign="top" style="padding-top:0px; padding-bottom:0px; padding-left:0px; padding-right:0px;"  id="B1495563714781" class="ao_SpacerBlock">
                        <a name="B1495563714781"></a>
                        <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%">
                          <tr>
                            <td style="padding:5px;font-size: 0px; line-height:0px;" width="100%">
                              <table border="0" cellspacing="0" cellpadding="0" height="4" width="100%" style="font-size: 0px; line-height: 0px">
                                <tr>
                                  <td height="4" width="100%" style="border-collapse:collapse;font-size:0px; line-height:0px;" bgcolor="#065D92">
                                    <img src="//news.finance-commerce.com/acton/image/transpix.gif" alt="" height="1" width="1" />
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr> -->
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr> 
<?php  } }  } ?>
</table>
</td></tr>
  <tr>
    <td width="100%">
      <table border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse;" width="600" class="devicewidth">
        <tbody><tr>
          <td valign="top" width="600" class="devicewidth" bgcolor="transparent" style="border-collapse: collapse; box-sizing: border-box; border-top: none; border-right: none; border-left: none; border-bottom: none;">
            <table cellspacing="0" cellpadding="0" width="100%" height="100%" style="width: 100%; height: 100%; border-collapse: separate;">
              <tbody><tr>
                <td valign="top" class="mcol1" style="border: 0; border-collapse: separate; width: 100%; height: 100%; ">
                  <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tbody><tr>
                      <td valign="top" style="padding-top:0px; padding-bottom:0px; padding-left:0px; padding-right:0px;" id="B1493912871674" class="ao_SpacerBlock">
                        <a name="B1493912871674"></a>
                        <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%">
                          <tbody><tr>
                            <td style="padding:5px;font-size: 0px; line-height:0px;" width="100%">
                              <table border="0" cellspacing="0" cellpadding="0" height="4" width="100%" style="font-size: 0px; line-height: 0px">
                                <tbody><tr>
                                  <td height="4" width="100%" style="border-collapse:collapse;font-size:0px; line-height:0px;" bgcolor="#065D92">
                                    <img src="//news.finance-commerce.com/acton/image/transpix.gif" alt="" height="1" width="1">
                                  </td>
                                </tr>
                              </tbody></table>
                            </td>
                          </tr>
                        </tbody></table>
                      </td>
                    </tr>
                  </tbody></table>
                </td>
              </tr>
            </tbody></table>
          </td>
        </tr>
      </tbody></table>
    </td>
  </tr>
  
<tr>
  <td width="100%" bgcolor="#FFFFFF" valign="top" align="center" class="mifooter" style="padding: 0 0 0 0 ; text-align: center;">
          <?php
           if (have_posts()) : while (have_posts()) : the_post(); ?>
             <?php the_content(); ?>
            <?php endwhile; ?>
          <?php endif; ?>
  </td>
</tr>

 <?php echo get_field('auction_footer'); ?>

</table>
</td>
<!-- no space  -->
<td width="4" bgcolor="#FFFFFF" style="font-size: 0; line-height: 0;"><img src="//news.finance-commerce.com/cdnr/50/acton/imgs/t/4/1/t.gif" border="0" width="4" height="1"></td>
</tr>
<tr>
  <td width="9" height="9" rowspan="2" colspan="2" style="font-size: 1px; line-height: 1px; background-size: 100%;" background="https://news.finance-commerce.com/cdnr/50/acton/imgs/rc/4/9/aaaaaa/FFFFFF/FFFFFF/bl/c.png" >
    <!--[if mso]><img border="0" width="9" height="9" src="//news.finance-commerce.com/cdnr/50/acton/imgs/rc/4/9/aaaaaa/FFFFFF/FFFFFF/bl/c.png" ><![endif]--><!--[if !mso]><!--><img border="0" width="9" height="1" src="//news.finance-commerce.com/cdnr/50/acton/imgs/t.gif" ><!--<![endif]-->
  </td>
  <!-- nospace
    -->
  <td width="590" height="5" bgcolor="#FFFFFF" class="devicewidthborder" style="font-size: 0; line-height: 0;" valign="top"><img src="//news.finance-commerce.com/cdnr/50/acton/imgs/t/590/1/t.gif" border="0" width="590" height="1" class="devicewidthborder"></td>
  <!-- no space
    -->
  <td width="9" height="9" rowspan="2" colspan="2" style="font-size: 1px; line-height: 1px; background-size: 100%;" background="https://news.finance-commerce.com/cdnr/50/acton/imgs/rc/4/9/aaaaaa/FFFFFF/FFFFFF/br/c.png" >
    <!--[if mso]><img border="0" width="9" height="9" src="//news.finance-commerce.com/cdnr/50/acton/imgs/rc/4/9/aaaaaa/FFFFFF/FFFFFF/br/c.png" ><![endif]--><!--[if !mso]><!--><img border="0" width="9" height="1" src="//news.finance-commerce.com/cdnr/50/acton/imgs/t.gif" ><!--<![endif]-->
  </td>
</tr>
<tr>
  <td width="590" height="4" bgcolor="#FFFFFF" class="devicewidthborder" style="font-size: 0; line-height: 0;" valign="top"><img src="//news.finance-commerce.com/cdnr/50/acton/imgs/t/590/1/t.gif" border="0" width="590" height="1" class="devicewidthborder"></td>
</tr>
</table>
<table width="608" border="0" cellspacing="0" cellpadding="0" class="devicewidthouter">
  <tr>
    <td width="100%" height="100" style="height: 100px; line-height: 100px;" class="devicewidthouter">&nbsp;</td>
  </tr>
</table>
</td></tr></table>
</div>
