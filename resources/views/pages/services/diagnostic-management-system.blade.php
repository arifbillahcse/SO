@extends('layouts.app')
@section('title', 'DiagnoCare Diagnostic Center Software in Bangladesh | Softorio')
@section('description', 'Explore DiagnoCare by Softorio: diagnostic center billing, samples, lab results, reports, owner finance and modules. Request a demonstration for your center.')
@section('image', asset('images/diagnocare/screen-billing.webp'))
@section('breadcrumbs', json_encode([
    ['name' => 'Home', 'url' => route('home')],
    ['name' => 'Services', 'url' => route('services')],
    ['name' => 'DiagnoCare', 'url' => route('diagnostic-management-system')],
]))
@section('page_schema', json_encode([
    '@context' => 'https://schema.org', '@type' => 'WebPage',
    'name' => 'DiagnoCare diagnostic center software', 'url' => route('diagnostic-management-system'),
    'mainEntity' => [
        '@type' => 'SoftwareApplication', 'name' => 'DiagnoCare',
        'applicationCategory' => 'BusinessApplication', 'operatingSystem' => 'Web browser',
        'url' => route('diagnostic-management-system'),
        'description' => 'Diagnostic center billing, laboratory workflows, reports and owner operations by Softorio.',
        'image' => asset('images/diagnocare/screen-billing.webp'),
        'screenshot' => asset('images/diagnocare/screen-billing.webp'),
        'provider' => ['@type' => 'Organization', 'name' => 'Softorio', 'url' => route('home')],
    ],
    'hasPart' => ['@type' => 'FAQPage', 'mainEntity' => [
        ['@type' => 'Question', 'name' => "What is DiagnoCare?", 'acceptedAnswer' => ['@type' => 'Answer', 'text' => "DiagnoCare is software for diagnostic centers and pathology labs in Bangladesh. Reception bills the patient, the lab enters and verifies results, and the report prints on your own letterhead. The owner sees the day's collection and dues on a phone."]],
        ['@type' => 'Question', 'name' => "How much does it cost?", 'acceptedAnswer' => ['@type' => 'Answer', 'text' => "It is a monthly or yearly subscription in three plans: Basic, Standard and Premium. The price depends on which parts you use and how many branches you have. Ask us for the current price list. Contact us to confirm current plans, pricing and included services."]],
        ['@type' => 'Question', 'name' => "Do we need to install anything?", 'acceptedAnswer' => ['@type' => 'Answer', 'text' => "No. It opens in a web browser on any computer, tablet or phone. You need internet and a printer. Only analyzer connection needs a small program on one lab computer."]],
        ['@type' => 'Question', 'name' => "What happens if the internet goes down?", 'acceptedAnswer' => ['@type' => 'Answer', 'text' => "Billing and result entry need internet, so we suggest a mobile data backup at the counter. Analyzer results are kept on the lab computer and sent as soon as the connection is back, so none are lost."]],
        ['@type' => 'Question', 'name' => "Can it take results from our analyzers?", 'acceptedAnswer' => ['@type' => 'Answer', 'text' => "Yes, if the analyzer sends results by ASTM or HL7, over a serial cable or the network. Tell us your models and we will confirm before you sign up."]],
        ['@type' => 'Question', 'name' => "Can we move our current test list and prices?", 'acceptedAnswer' => ['@type' => 'Answer', 'text' => "Yes. Send us your test list, prices and referral doctors in Excel. We check the file first and import it in one go, so nothing is half loaded."]],
        ['@type' => 'Question', 'name' => "Will the reports look like ours?", 'acceptedAnswer' => ['@type' => 'Answer', 'text' => "Yes. Reports and receipts use your name, logo, letterhead margins and doctors' signatures. We match them to the papers you use today before you go live."]],
        ['@type' => 'Question', 'name' => "Can someone change a result after it is verified?", 'acceptedAnswer' => ['@type' => 'Answer', 'text' => "Not quietly. A verified result can only be changed through a correction, which keeps the old value, the new value, the reason and who made it. Bills work the same way, with refunds and adjustments instead of edits."]],
        ['@type' => 'Question', 'name' => "Who owns our data?", 'acceptedAnswer' => ['@type' => 'Answer', 'text' => "You do. Every clinic has its own database, and you can export your data at any time."]],
        ['@type' => 'Question', 'name' => "How long does setup take?", 'acceptedAnswer' => ['@type' => 'Answer', 'text' => "Setup depends on your test catalog, report formats, data migration and analyzer requirements. We agree on the scope and schedule with you before rollout."]],
    ]],
]))
@push('head')
@vite(['resources/css/diagnocare.css', 'resources/js/diagnocare.js'])
@endpush
@section('content')
<main class="diagnocare-page">

    <!-- Hero -->
    <section class="dg-hero">
      <div class="dg-w dg-hero-in">
        <div class="dg-hero-copy">
          <nav class="dg-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><a href="{{ route('services') }}">Services</a><span>/</span><span>DiagnoCare</span></nav>
          <p class="dg-kicker dg-in">DiagnoCare by Softorio &middot; Built for Bangladesh</p>
          <h1 class="dg-in dg-d1">From the reception counter to the signed report.</h1>
          <p class="dg-lead dg-in dg-d2">Bill the patient, collect the sample, verify the result and print it on your own letterhead. At night the cash in the drawer matches the screen, and you can check it from home.</p>
          <div class="dg-cta dg-in dg-d3">
            <a class="dg-btn" href="#demo">Book a demo</a>
            @if($diagnosticWhatsappUrl)
            <a class="dg-btn dg-btn-alt" href="{{ $diagnosticWhatsappUrl }}" target="_blank" rel="noopener noreferrer">Message us on WhatsApp</a>
            @else
            <a class="dg-btn dg-btn-alt" href="#views">Explore the software</a>
            @endif
          </div>
          <div class="dg-rack dg-in dg-d4" aria-hidden="true">
            <span class="dg-drop"></span>
            <span class="dg-tube" style="--c:#ff5470;--h:62%;--d:0s"><b></b></span>
            <span class="dg-tube" style="--c:#7c4dff;--h:44%;--d:-1.2s"><b></b></span>
            <span class="dg-tube" style="--c:#12b886;--h:70%;--d:-2.4s"><b></b></span>
            <span class="dg-tube" style="--c:#ffb020;--h:52%;--d:-3.1s"><b></b></span>
            <span class="dg-tube" style="--c:#3B82F6;--h:58%;--d:-0.6s"><b></b></span>
          </div>
        </div>

        <div class="dg-hero-shot dg-in dg-d2">
          <figure class="dg-frame">
            <div class="dg-frame-bar" aria-hidden="true"><span></span><span></span><span></span><em>DiagnoCare &middot; New bill &middot; Sample data</em></div>
            <img src="{{ asset('images/diagnocare/screen-billing.webp') }}" width="1600" height="956" alt="DiagnoCare new bill screen with four tests, a 100 taka discount and a bKash payment of 2,285 taka" fetchpriority="high">
          </figure>
          <figure class="dg-phone" aria-hidden="true">
            <img src="{{ asset('images/diagnocare/screen-phone.webp') }}" width="560" height="953" alt="" loading="lazy">
          </figure>
          <ul class="dg-toasts" aria-hidden="true">
            <li><i class="dg-g"></i>bKash payment received <b>৳ 2,285</b></li>
            <li><i class="dg-b"></i>Sample 2611160004 collected</li>
            <li><i class="dg-a"></i>Lipid profile verified</li>
          </ul>
        </div>
      </div>
    </section>

    <!-- Facts strip -->
    <div class="dg-strip" aria-label="Highlights">
      <div class="dg-marquee">
        <ul>
          <li>Prints on your letterhead</li><li>bKash, Nagad, card and cash</li><li>ASTM and HL7 analyzers</li><li>Separate database for every clinic</li><li>Works on any browser</li><li>Owner reports on your phone</li>
        </ul>
        <ul aria-hidden="true">
          <li>Prints on your letterhead</li><li>bKash, Nagad, card and cash</li><li>ASTM and HL7 analyzers</li><li>Separate database for every clinic</li><li>Works on any browser</li><li>Owner reports on your phone</li>
        </ul>
      </div>
    </div>

    <!-- Software views slider (real screens) -->
    <section id="views">
      <div class="dg-w">
        <h2 class="dg-rv">Software views</h2>
        <p class="dg-lead dg-rv">Explore DiagnoCare with sample data. Swipe, use the arrows, or choose a screen below.</p>

        <div class="dg-slider dg-rv" data-slider aria-roledescription="carousel" aria-label="DiagnoCare screens">
          <div class="dg-slides">
            <figure class="dg-slide" aria-roledescription="slide" aria-label="1 of 6">
              <img loading="lazy" src="{{ asset('images/diagnocare/slides/dashboard.webp') }}" width="1600" height="1000" alt="Dashboard with this month's patient visits, billed and collected amounts, lab work waiting, and collections for the last 14 days">
              <figcaption><b>Dashboard</b> This month's visits, billing and collection, lab work waiting, and a 14-day collection chart.</figcaption>
            </figure>
            <figure class="dg-slide" aria-roledescription="slide" aria-label="2 of 6">
              <img src="{{ asset('images/diagnocare/screen-billing.webp') }}" width="1600" height="956" loading="lazy" alt="New bill with four tests, tubes and needle added automatically, a discount with a reason and a bKash payment">
              <figcaption><b>New bill</b> Patient, tests, discount and payment on one screen. Tubes and needles are added for you.</figcaption>
            </figure>
            <figure class="dg-slide" aria-roledescription="slide" aria-label="3 of 6">
              <img src="{{ asset('images/diagnocare/slides/samples.webp') }}" width="1600" height="1000" loading="lazy" alt="Samples list with tube colours, collect, receive in lab, print label and reject actions">
              <figcaption><b>Samples</b> Tube colour for each sample, barcode labels, and collect, receive or reject with one click.</figcaption>
            </figure>
            <figure class="dg-slide" aria-roledescription="slide" aria-label="4 of 6">
              <img src="{{ asset('images/diagnocare/slides/results.webp') }}" width="1600" height="956" loading="lazy" alt="Lipid profile results waiting for verification with Verify and release and Send back">
              <figcaption><b>Result verification</b> A second person checks the values against the ranges, then releases or sends back.</figcaption>
            </figure>
            <figure class="dg-slide" aria-roledescription="slide" aria-label="5 of 6">
              <img src="{{ asset('images/diagnocare/slides/imaging.webp') }}" width="1600" height="1000" loading="lazy" alt="Ultrasound report for whole abdomen written from a fatty liver template, ready to sign">
              <figcaption><b>Imaging report</b> Start from a template, add phrases, save a draft, and sign when it is ready.</figcaption>
            </figure>
            <figure class="dg-slide" aria-roledescription="slide" aria-label="6 of 6">
              <img src="{{ asset('images/diagnocare/slides/profit.webp') }}" width="1600" height="1000" loading="lazy" alt="Profit report with money in and out, commission, expenses by category and collections by payment method">
              <figcaption><b>Profit report</b> Money in and out, commission, expenses by category, and collection by bKash, Nagad, card and cash.</figcaption>
            </figure>
          </div>
          <button class="dg-nav-btn dg-prev" type="button" aria-label="Previous screen">‹</button>
          <button class="dg-nav-btn dg-next" type="button" aria-label="Next screen">›</button>
          <div class="dg-dots" role="group" aria-label="Choose a screen"></div>
        </div>
      </div>
    </section>

    <!-- Bento features -->
    <section id="features" class="dg-pt0">
      <div class="dg-w">
        <h2 class="dg-rv">Built around how a diagnostic center actually works</h2>
        <div class="dg-bento">
          <article class="dg-bx dg-b1 dg-rv">
            <h3>Every taka accounted for</h3>
            <p>Part payments, discounts, refunds and dues, with a receipt for each one.</p>
            <div class="dg-bill-lines" aria-hidden="true">
              <div><span>CBC, Lipid profile, FBS, TSH</span><b>৳ 2,350</b></div>
              <div><span>Tubes and needle (auto)</span><b>৳ 35</b></div>
              <div><span>Discount, regular patient</span><b>− ৳ 100</b></div>
              <div><span>Paid by bKash</span><b>৳ 2,285</b></div>
              <div class="dg-tot"><span>Due</span><b class="dg-ok">৳ 0</b></div>
            </div>
          </article>
          <article class="dg-bx dg-b2 dg-rv">
            <h3>Flags while you type</h3>
            <p>Total cholesterol 260 mg/dL against a limit of 200.</p>
            <div class="dg-range" aria-hidden="true"><span class="dg-zone"></span><i style="--x:86%"></i></div>
            <small>Printed in bold with an H</small>
          </article>
          <article class="dg-bx dg-b3 dg-dark dg-rv">
            <h3>Nothing is overwritten</h3>
            <p>A verified result or a saved bill changes only through a correction. The old value stays, with the reason and the name of the person who changed it.</p>
            <div class="dg-amend" aria-hidden="true"><s>HDL 48 mg/dL</s><span>HDL 35 mg/dL · re-run, sample haemolysed · Dr. Rahman</span></div>
          </article>
          <article class="dg-bx dg-b4 dg-rv">
            <h3>Referral doctors</h3>
            <p>Picked on every bill, so you can see who sends you patients each month.</p>
          </article>
          <article class="dg-bx dg-b5 dg-rv">
            <h3>Collection centres</h3>
            <p>Samples travel to the main lab in a tracked box, with a courier list.</p>
          </article>
          <article class="dg-bx dg-b6 dg-rv">
            <h3>Imaging reports</h3>
            <p>Templates for X-ray, ultrasound, CT and MRI that the doctor edits and signs.</p>
          </article>
        </div>
      </div>
    </section>

    <!-- Every feature, module by module -->
    <section id="all-features" class="dg-pt0">
      <div class="dg-w">
        <h2 class="dg-rv">Everything DiagnoCare does</h2>
        <p class="dg-lead dg-rv">Explore the software module by module. Click a screenshot to see it larger. Confirm your required modules, device compatibility and rollout scope with our team.</p>

        <div class="dg-filters dg-rv" role="toolbar" aria-label="Show features for">
          <button type="button" class="dg-chip dg-on" data-filter="all">All</button>
          <button type="button" class="dg-chip" data-filter="front">Front desk</button>
          <button type="button" class="dg-chip" data-filter="lab">Laboratory</button>
          <button type="button" class="dg-chip" data-filter="imaging">Imaging</button>
          <button type="button" class="dg-chip" data-filter="money">Owner and finance</button>
          <button type="button" class="dg-chip" data-filter="ops">Stock and staff</button>
          <button type="button" class="dg-chip" data-filter="admin">Admin and security</button>
        </div>

        <div class="dg-fgroups">
          <article class="dg-fg dg-rv" data-group="front">
            <div class="dg-fg-head"><span class="dg-fg-tag">Front desk and billing</span><h3>Patients, bills and cash</h3></div>
            <ul class="dg-ticks dg-cols">
              <li>Patient records with a code, searched by name, code or mobile number</li>
              <li>Age in years, or in months and days for babies</li>
              <li>One-screen billing with keyboard shortcuts (Enter adds a test, Ctrl+S saves, F2 starts a new bill)</li>
              <li>Test and service list with categories, prices and packages</li>
              <li>Tubes, needles and film added to the bill automatically for each test</li>
              <li>Discount by percent or taka, with a reason, and a limit for each role</li>
              <li>Cash, card, bKash and Nagad, with the transaction ID kept</li>
              <li>Part payment, dues list, and later payments against the same bill</li>
              <li>Refunds that wait for approval</li>
              <li>Cash sessions per cashier: opening cash, expected, counted and difference</li>
              <li>Invoice with patient and office copies, amount in words and barcode</li>
              <li>Money receipt for every payment, and sample labels</li>
              <li>Referral doctor and report delivery time on every bill</li>
              <li>Bill, receipt and order numbers in your own format</li>
            </ul>
            <div class="dg-shots">
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/order.webp') }}" width="900" height="563" loading="lazy" alt="Order page with patient, invoice and tests"><span>Order page</span></button>
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/invoice.webp') }}" width="900" height="1171" loading="lazy" alt="Printed invoice with patient copy, tests, discount, bKash payment and amount in words"><span>Invoice</span></button>
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/receipt.webp') }}" width="645" height="900" loading="lazy" alt="Money receipt for a bKash payment"><span>Money receipt</span></button>
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/dues.webp') }}" width="900" height="600" loading="lazy" alt="Dues list with total, paid and due for each invoice"><span>Dues</span></button>
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/cash.webp') }}" width="900" height="600" loading="lazy" alt="Cash sessions with opening, expected and counted cash"><span>Cash sessions</span></button>
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/patients.webp') }}" width="900" height="600" loading="lazy" alt="Patient list with codes, age, sex and phone"><span>Patients</span></button>
            </div>
          </article>

          <article class="dg-fg dg-rv" data-group="lab">
            <div class="dg-fg-head"><span class="dg-fg-tag">Laboratory and lab reports</span><h3>From sample to signed report</h3></div>
            <ul class="dg-ticks dg-cols">
              <li>Test set-up with departments, sample types and values</li>
              <li>Values as numbers, short text or a choice from a list</li>
              <li>Reference ranges by age and sex</li>
              <li>Sample types with tube colour (lavender, red, grey top and more)</li>
              <li>Barcode labels for each sample</li>
              <li>Collect, receive in lab, or reject with a reason</li>
              <li>Work list: to enter, to verify, waiting for sample, released</li>
              <li>Flags for high, low, critical and abnormal values</li>
              <li>Verify and release, or send back with a note</li>
              <li>Corrections saved as new versions, never overwritten</li>
              <li>PDF report grouped by department, with barcodes and signatures</li>
              <li>Plain paper with your header, or space left for a pre-printed pad</li>
              <li>Header and footer banners, and a faint logo behind the results</li>
              <li>Report signatures for each doctor, with qualifications</li>
              <li>Reports held back while a due is unpaid, unless allowed</li>
            </ul>
            <div class="dg-shots">
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/screen-report.webp') }}" width="1000" height="877" loading="lazy" alt="Printed lab report with flagged high and low values"><span>Lab report</span></button>
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/tests.webp') }}" width="900" height="600" loading="lazy" alt="Lab tests list with department and sample type"><span>Lab tests</span></button>
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/analytes.webp') }}" width="900" height="600" loading="lazy" alt="Analytes with kind, unit and number of ranges"><span>Analytes and ranges</span></button>
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/layout.webp') }}" width="900" height="600" loading="lazy" alt="Report layout settings for paper, footer and banner images"><span>Report layout</span></button>
            </div>
          </article>

          <article class="dg-fg dg-rv" data-group="lab">
            <div class="dg-fg-head"><span class="dg-fg-tag">Analyzers, branches and collection centres</span><h3>Machines and more than one site</h3></div>
            <ul class="dg-ticks dg-cols">
              <li>Analyzer connection by ASTM and HL7, serial cable or network</li>
              <li>Test codes mapped for each machine</li>
              <li>Machine inbox that shows results it could not match</li>
              <li>Results wait on the lab PC when the internet is down</li>
              <li>Branch switcher at the top of every screen</li>
              <li>Lists and reports by branch, and branch details on printed papers</li>
              <li>Collection centres that send samples to another branch's lab</li>
              <li>Sample transfers in a box, with a courier list and "receive all"</li>
            </ul>
            <div class="dg-shots">
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/machines.webp') }}" width="900" height="600" loading="lazy" alt="Lab machines list with protocol ASTM and codes mapped"><span>Lab machines</span></button>
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/machines-inbox.webp') }}" width="900" height="600" loading="lazy" alt="Machine inbox with messages that need attention"><span>Machine inbox</span></button>
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/transfers.webp') }}" width="900" height="600" loading="lazy" alt="Sample transfer from North branch to Main branch on the way"><span>Sample transfers</span></button>
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/centres.webp') }}" width="900" height="600" loading="lazy" alt="Collection centre settings choosing where each branch's samples are tested"><span>Collection centres</span></button>
            </div>
          </article>

          <article class="dg-fg dg-rv" data-group="imaging">
            <div class="dg-fg-head"><span class="dg-fg-tag">Imaging reports and patient portal</span><h3>Ultrasound, X-ray, CT and MRI reports</h3></div>
            <ul class="dg-ticks dg-cols">
              <li>Templates for each imaging test, for male, female or both</li>
              <li>Ready example templates, and copying a template to another test</li>
              <li>Saved phrases added with one click</li>
              <li>Draft, sign, and new versions when a signed report changes</li>
              <li>Printed on your letterhead, alone or with the lab reports</li>
              <li>Reports past their delivery time shown on the dashboard</li>
              <li>Patient portal: patients open their reports with the bill number and mobile number</li>
              <li>"Report ready" message with a link to the patient's page</li>
            </ul>
            <div class="dg-shots">
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/imaging-templates.webp') }}" width="900" height="600" loading="lazy" alt="Imaging report templates for whole abdomen ultrasound"><span>Report templates</span></button>
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/portal.webp') }}" width="560" height="718" loading="lazy" alt="Patient portal on a phone asking for bill number and mobile number"><span>Patient portal</span></button>
            </div>
          </article>

          <article class="dg-fg dg-rv" data-group="money">
            <div class="dg-fg-head"><span class="dg-fg-tag">Owner reports and finance</span><h3>Know where the money went</h3></div>
            <ul class="dg-ticks dg-cols">
              <li>Dashboard for today and this month, with a 14-day collection chart</li>
              <li>Sales reports by day, test, doctor, cashier and branch</li>
              <li>Accounts for cash, bank, bKash, Nagad and card, with transfers between them</li>
              <li>Money held in each account</li>
              <li>Expenses with categories</li>
              <li>Referral commission rates per doctor, earned only on paid tests</li>
              <li>Commission payouts, taken back if a test is cancelled</li>
              <li>Profit report: collected, refunded, expenses, commission and what is left</li>
            </ul>
            <div class="dg-shots">
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/sales.webp') }}" width="900" height="600" loading="lazy" alt="Sales report day by day with orders, patients, billed, collected and refunded"><span>Sales reports</span></button>
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/accounts.webp') }}" width="900" height="600" loading="lazy" alt="Accounts for bKash, card, Nagad and cash with balances"><span>Accounts</span></button>
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/commission.webp') }}" width="900" height="600" loading="lazy" alt="Referral commission by doctor with earned, paid and owed"><span>Referral commission</span></button>
            </div>
          </article>

          <article class="dg-fg dg-rv" data-group="ops">
            <div class="dg-fg-head"><span class="dg-fg-tag">Inventory, staff and messages</span><h3>Reagents, people and SMS</h3></div>
            <ul class="dg-ticks dg-cols">
              <li>Stock items for reagents and consumables, with suppliers</li>
              <li>Purchases with batches and expiry dates, printable</li>
              <li>Stock taken off automatically for each test done</li>
              <li>Manual issue, wastage and stock count</li>
              <li>Low stock, expiring batches, and tests done without stock</li>
              <li>Staff records with designation, branch and salary</li>
              <li>Daily attendance, with "mark the rest present, weekly off or holiday"</li>
              <li>Monthly attendance, advances, salary runs, payslips and salary sheet</li>
              <li>SMS and email: bill confirmation, report ready and due reminders</li>
              <li>Message wording you can edit, with the SMS part count</li>
              <li>Message log with retries</li>
            </ul>
            <div class="dg-shots">
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/stock.webp') }}" width="900" height="600" loading="lazy" alt="Stock items with stock level, value and expiry tracking"><span>Stock</span></button>
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/attendance.webp') }}" width="900" height="600" loading="lazy" alt="Daily attendance sheet for staff"><span>Daily attendance</span></button>
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/salary.webp') }}" width="900" height="600" loading="lazy" alt="Salary runs by month with net pay and status"><span>Salary runs</span></button>
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/wording.webp') }}" width="900" height="600" loading="lazy" alt="Message wording for SMS and email due reminders, bill confirmation and report ready"><span>Message wording</span></button>
            </div>
          </article>

          <article class="dg-fg dg-rv" data-group="admin">
            <div class="dg-fg-head"><span class="dg-fg-tag">Admin and security</span><h3>Control who does what</h3></div>
            <ul class="dg-ticks dg-cols">
              <li>Staff accounts, one per person</li>
              <li>Roles with detailed permissions, plus ready roles for reception, lab, verifier and accountant</li>
              <li>Two-step sign-in with an authenticator app</li>
              <li>Audit log of every change to money, results and permissions</li>
              <li>Modules switched on and off, with their data kept</li>
              <li>Data export of everything as CSV files, in one zip</li>
              <li>Clinic profile, logo and report header text</li>
              <li>Separate database for each clinic, with backup arrangements agreed during setup</li>
              <li>Subscription page with your plan and invoices</li>
            </ul>
            <div class="dg-shots">
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/roles.webp') }}" width="900" height="600" loading="lazy" alt="Roles with number of permissions and users"><span>Roles</span></button>
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/audit.webp') }}" width="900" height="600" loading="lazy" alt="Audit log of actions by user"><span>Audit log</span></button>
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/modules.webp') }}" width="900" height="600" loading="lazy" alt="Modules page with each module switched on or off"><span>Modules</span></button>
              <button class="dg-shot" type="button"><img src="{{ asset('images/diagnocare/features/export.webp') }}" width="900" height="600" loading="lazy" alt="Data export with download everything as a zip"><span>Data export</span></button>
            </div>
          </article>
        </div>
      </div>
    </section>

    <dialog class="dg-lightbox" id="lightbox" aria-label="Screenshot">
      <button type="button" class="dg-lb-close" aria-label="Close">×</button>
      <img alt="">
      <p></p>
    </dialog>

    <!-- How a day flows -->
    <section class="dg-pt0">
      <div class="dg-w">
        <h2 class="dg-rv">One patient, start to finish</h2>
        <ol class="dg-flow dg-rv">
          <li><em>1</em><h3>Register and bill</h3><p>Mobile number, tests, payment. Receipt and sample labels print.</p></li>
          <li><em>2</em><h3>Collect</h3><p>Scan the label when the sample is taken and again when it reaches the lab.</p></li>
          <li><em>3</em><h3>Enter and verify</h3><p>Typed or from the analyzer, then checked by a second person.</p></li>
          <li><em>4</em><h3>Deliver</h3><p>Printed at the counter or downloaded by the patient. Any due is collected first.</p></li>
        </ol>
      </div>
    </section>

    <!-- Modules -->
    <section id="modules" class="dg-pt0">
      <div class="dg-w dg-mods">
        <div>
          <h2 class="dg-rv">Pay for the parts you use</h2>
          <p class="dg-lead dg-rv">DiagnoCare is built from modules. Switch one off and anything that depends on it goes off too. Your data stays, ready for when you switch it back on. Try it.</p>
          <div class="dg-switches dg-rv" data-modules>
            <button class="dg-sw" type="button" data-m="frontdesk" aria-pressed="true"><span class="dg-t"></span>Front desk and billing</button>
            <button class="dg-sw" type="button" data-m="laboratory" data-needs="frontdesk" aria-pressed="true"><span class="dg-t"></span>Laboratory<small>needs Front desk</small></button>
            <button class="dg-sw" type="button" data-m="reports" data-needs="laboratory" aria-pressed="true"><span class="dg-t"></span>Lab reports<small>needs Laboratory</small></button>
            <button class="dg-sw" type="button" data-m="analyzers" data-needs="laboratory" aria-pressed="true"><span class="dg-t"></span>Analyzer connection<small>needs Laboratory</small></button>
            <button class="dg-sw" type="button" data-m="portal" data-needs="reports" aria-pressed="true"><span class="dg-t"></span>Patient portal<small>needs Lab reports</small></button>
            <button class="dg-sw" type="button" data-m="imaging" data-needs="frontdesk" aria-pressed="true"><span class="dg-t"></span>Imaging reports<small>needs Front desk</small></button>
          </div>
        </div>
        <div class="dg-menu-card dg-rv" aria-live="polite">
          <h3>Module example &middot; Your staff see</h3>
          <div data-s="frontdesk">New bill, orders, dues, refunds</div>
          <div data-s="laboratory">Samples and work list</div>
          <div data-s="reports">Report printing</div>
          <div data-s="analyzers">Lab machines inbox</div>
          <div data-s="portal">Patient report downloads</div>
          <div data-s="imaging">Imaging work list</div>
          <p id="mod-count">6 of 6 modules on</p><p class="dg-module-note">An interactive example of module dependencies. Your enabled modules are agreed during setup.</p>
        </div>
      </div>
    </section>

    <!-- Analyzer -->
    <section id="analyzers" class="dg-pt0">
      <div class="dg-w dg-split">
        <div>
          <h2 class="dg-rv">Analyzer results without retyping</h2>
          <p class="dg-lead dg-rv">Connect your biochemistry, haematology and hormone analyzers once. After that, results land on the work list by themselves, matched to the right sample by its barcode.</p>
          <ul class="dg-ticks dg-rv">
            <li>ASTM and HL7, over a serial cable or the network</li>
            <li>If the internet drops, results wait on the lab computer and go when it is back</li>
            <li>Staff still check every result before it is released</li>
          </ul>
        </div>
        <div class="dg-pipe dg-rv" aria-hidden="true">
          <div class="dg-node">Analyzer</div>
          <div class="dg-wire"><span></span><span></span><span></span></div>
          <div class="dg-node">Lab PC</div>
          <div class="dg-wire"><span></span><span></span><span></span></div>
          <div class="dg-node dg-hot">DiagnoCare</div>
        </div>
      </div>
    </section>

    <!-- Owner -->
    <section id="owner" class="dg-pt0">
      <div class="dg-w">
        <div class="dg-owner dg-rv">
          <div>
            <h2>Know how the day went before you get home</h2>
            <p class="dg-lead">Collection, dues and pending reports on your phone. Money taken by each staff member, and income by test, doctor and branch. No need to phone the counter.</p>
            <a class="dg-btn" href="#demo">See it on your phone</a>
          </div>
          <img class="dg-owner-phone" src="{{ asset('images/diagnocare/screen-phone.webp') }}" width="560" height="953" loading="lazy" alt="Owner dashboard on a phone showing billed 6,785 taka and collected 2,285 taka">
        </div>
      </div>
    </section>

    <!-- Safety -->
    <section id="security" class="dg-pt0">
      <div class="dg-w dg-two">
        <div class="dg-rv">
          <h2>Patient data, handled carefully</h2>
          <ul class="dg-checks">
            <li>Each clinic has its own database. Nothing is shared with other clinics.</li>
            <li>Backup arrangements, retention and restore checks are agreed as part of your hosting and support setup.</li>
            <li>Personal accounts for every staff member, with two-step sign-in.</li>
            <li>An audit log of every change to money, results and permissions.</li>
          </ul>
        </div>
        <div class="dg-rv">
          <h2>Access by role</h2>
          <ul class="dg-checks dg-alt">
            <li>Reception bills, but cannot change prices.</li>
            <li>Technicians enter results. Only those allowed can verify them.</li>
            <li>Large discounts and refunds can be kept for the owner.</li>
            <li>A staff member who leaves is switched off the same day. Their records stay.</li>
          </ul>
        </div>
      </div>
    </section>

    <!-- Plans -->
    <section id="plans" class="dg-pt0">
      <div class="dg-w">
        <h2 class="dg-rv">Plans</h2>
        <p class="dg-lead dg-rv">Discuss monthly or yearly subscription options with our team. Pay by bKash, Nagad or bank transfer, and you get an invoice for each payment.</p>
        <div class="dg-plans">
          <article class="dg-plan dg-rv">
            <h3>Basic</h3>
            <p class="dg-price">Ask for price</p>
            <p class="dg-for">A single lab</p>
            <ul class="dg-ticks"><li>Front desk and billing</li><li>Laboratory and verification</li><li>Lab reports on your letterhead</li><li>1 branch</li></ul>
            <a class="dg-btn dg-btn-alt" href="#demo" data-plan="Basic">Ask about Basic</a>
          </article>
          <article class="dg-plan dg-pop dg-rv">
            <p class="dg-tag">Lab + imaging</p>
            <h3>Standard</h3>
            <p class="dg-price">Ask for price</p>
            <p class="dg-for">Lab and imaging under one roof</p>
            <ul class="dg-ticks"><li>Everything in Basic</li><li>Imaging reports</li><li>Owner reports and finance</li><li>Patient portal</li><li>Inventory and SMS</li><li>1 branch</li></ul>
            <a class="dg-btn" href="#demo" data-plan="Standard">Ask about Standard</a>
          </article>
          <article class="dg-plan dg-rv">
            <h3>Premium</h3>
            <p class="dg-price">Ask for price</p>
            <p class="dg-for">Busy labs and groups</p>
            <ul class="dg-ticks"><li>Everything in Standard</li><li>Analyzer connection</li><li>Branches and collection centres</li><li>Staff attendance and payroll</li><li>Up to 3 branches</li></ul>
            <a class="dg-btn dg-btn-alt" href="#demo" data-plan="Premium">Ask about Premium</a>
          </article>
        </div>
      </div>
    </section>

    <!-- FAQ: keep in step with the FAQPage data in the head -->
    <section id="faq" class="dg-pt0">
      <div class="dg-w dg-narrow">
        <h2 class="dg-rv">Questions we get asked</h2>
        <div class="dg-faq dg-rv">
          <details><summary>What is DiagnoCare?</summary><p>DiagnoCare is software for diagnostic centers and pathology labs in Bangladesh. Reception bills the patient, the lab enters and verifies results, and the report prints on your own letterhead. The owner sees the day's collection and dues on a phone.</p></details>
          <details><summary>How much does it cost?</summary><p>It is a monthly or yearly subscription in three plans: Basic, Standard and Premium. The price depends on which parts you use and how many branches you have. Ask us for the current price list. Contact us to confirm current plans, pricing and included services.</p></details>
          <details><summary>Do we need to install anything?</summary><p>No. It opens in a web browser on any computer, tablet or phone. You need internet and a printer. Only analyzer connection needs a small program on one lab computer.</p></details>
          <details><summary>What happens if the internet goes down?</summary><p>Billing and result entry need internet, so we suggest a mobile data backup at the counter. Analyzer results are kept on the lab computer and sent as soon as the connection is back, so none are lost.</p></details>
          <details><summary>Can it take results from our analyzers?</summary><p>Yes, if the analyzer sends results by ASTM or HL7, over a serial cable or the network. Tell us your models and we will confirm before you sign up.</p></details>
          <details><summary>Can we move our current test list and prices?</summary><p>Yes. Send us your test list, prices and referral doctors in Excel. We check the file first and import it in one go, so nothing is half loaded.</p></details>
          <details><summary>Will the reports look like ours?</summary><p>Yes. Reports and receipts use your name, logo, letterhead margins and doctors' signatures. We match them to the papers you use today before you go live.</p></details>
          <details><summary>Can someone change a result after it is verified?</summary><p>Not quietly. A verified result can only be changed through a correction, which keeps the old value, the new value, the reason and who made it. Bills work the same way, with refunds and adjustments instead of edits.</p></details>
          <details><summary>Who owns our data?</summary><p>You do. Every clinic has its own database, and you can export your data at any time.</p></details>
          <details><summary>How long does setup take?</summary><p>Setup depends on your test catalog, report formats, data migration and analyzer requirements. We agree on the scope and schedule with you before rollout.</p></details>
        </div>
      </div>
    </section>

    <!-- Demo -->
    <section id="demo" class="dg-pt0">
      <div class="dg-w">
        <div class="dg-cta-box dg-rv">
          <div>
            <h2>See it with your own tests</h2>
            <p class="dg-lead">Tell us a little about your center. We will discuss your workflow, arrange a demonstration and confirm the modules and setup your center needs.</p>
            <p class="dg-contact">@if($diagnosticWhatsappUrl)<a href="{{ $diagnosticWhatsappUrl }}" target="_blank" rel="noopener noreferrer">Message us on WhatsApp &nearr;</a><br>@endif @if($siteContact['email'])<a href="mailto:{{ $siteContact['email'] }}">{{ $siteContact['email'] }}</a>@endif</p>
          </div>
          <form id="demo-form" method="POST" action="{{ route('contact.store') }}" data-endpoint="{{ route('contact.store') }}">
            @csrf
            <input type="hidden" name="service_interested" value="Diagnostic Management System - DiagnoCare demo">
            <div class="dg-form-row">
              <label for="dg-name">Your name<input id="dg-name" name="name" autocomplete="name" required maxlength="255"></label>
              <label for="dg-clinic">Clinic or lab<input id="dg-clinic" name="clinic_name" autocomplete="organization" required maxlength="255"></label>
            </div>
            <label for="dg-email">Email address<input id="dg-email" name="email" type="email" autocomplete="email" required maxlength="255"></label>
            <div class="dg-form-row">
              <label for="dg-phone">Mobile number<input id="dg-phone" name="phone" type="tel" autocomplete="tel" required maxlength="50" placeholder="Include your country code"></label>
              <label for="f-plan">Plan<select name="demo_plan" id="f-plan"><option>Not sure yet</option><option>Basic</option><option>Standard</option><option>Premium</option></select></label>
            </div>
            <label for="dg-message">What would you like to see?<textarea id="dg-message" name="message" rows="3" maxlength="5000" required>I would like a DiagnoCare demonstration for my center.</textarea></label>
            <div class="dg-honeypot" aria-hidden="true"><label>Leave this empty<input name="company_website" tabindex="-1" autocomplete="off"></label></div>
            <button class="dg-btn cta-submit" type="submit">Request a demo <span aria-hidden="true">&nearr;</span></button>
            <p class="dg-form-note">Your request goes directly to the Softorio team. <a href="{{ route('privacy') }}">Privacy policy</a></p>
          </form>
        </div>
      </div>
    </section>

<section class="dg-rollout"><div class="dg-w"><p>Need a tailored implementation or additional modules? <a href="{{ route('diagnostic-centers-solution') }}">Explore our custom rollout approach &nearr;</a></p></div></section>

</main>
@endsection
