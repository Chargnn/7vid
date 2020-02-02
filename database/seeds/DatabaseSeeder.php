<?php

use App\Category;
use App\ChannelSetting;
use App\Comment;
use App\CommentVote;
use App\Country;
use App\Subscription;
use App\User;
use App\Video;
use App\VideoSetting;
use App\VideoVote;
use App\Views;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $faker = Faker\Factory::create();

        $maxVideosCount = 1000;
        $maxUserCount = 1000;
        $maxCommentsCount = 1000;
        $maxVotesCount = 1000;
        $maxSubCount = $faker->numberBetween($maxUserCount / 2, $maxUserCount);

        echo 'Seed countries' . PHP_EOL;
        $this->seedCountries();

        echo 'Seed categories' . PHP_EOL;
        $this->seedCategories();

        echo 'Users' . PHP_EOL;
        factory(User::class, $maxUserCount)->create();
        echo 'Channel settings' . PHP_EOL;

        /** @var User $user */
        foreach (User::all() as $user) {
            /** @var ChannelSetting $setting */
            $setting = new ChannelSetting();
            $setting->setAbout($faker->realText(200));
            $setting->setBackgroundImage('channel-banner.png');
            $setting->setChannelId($user->getId());
            $setting->save();
        }
        echo 'Videos' . PHP_EOL;
        \factory(Video::class, $maxVideosCount)->create();
        echo 'Videos settings' . PHP_EOL;

        /** @var Video $video */
        foreach (Video::all() as $video) {
            /** @var VideoSetting $setting */
            $setting = new VideoSetting();
            $setting->setVideoId($video->getId());
            $setting->setPrivate($faker->boolean);
            $setting->setAllowComments($faker->boolean);
            $setting->setAllowVotes($faker->boolean);
            $setting->save();
        }

        echo 'Comments' . PHP_EOL;
        \factory(Comment::class, $maxCommentsCount)->create();
        echo 'Video Votes' . PHP_EOL;
        \factory(VideoVote::class, $maxVotesCount)->create();
        echo 'Comment Votes' . PHP_EOL;
        \factory(CommentVote::class, $maxVotesCount)->create();
        echo 'Subscriptions' . PHP_EOL;
        \factory(Subscription::class, $maxSubCount)->create();
        echo 'Views' . PHP_EOL;
        \factory(Views::class, $maxVideosCount)->create();
    }

    /**
     * Insert all country entries
     */
    private function seedCountries(): void
    {
        /** @var array $countries */
        $countries = [
            ['NUL', 'Not specified', 'XX'],
            ['AFG', 'Afghanistan', 'AF'],
            ['ALA', 'Åland', 'AX'],
            ['ALB', 'Albania', 'AL'],
            ['DZA', 'Algeria', 'DZ'],
            ['ASM', 'American Samoa', 'AS'],
            ['AND', 'Andorra', 'AD'],
            ['AGO', 'Angola', 'AO'],
            ['AIA', 'Anguilla', 'AI'],
            ['ATA', 'Antarctica', 'AQ'],
            ['ATG', 'Antigua and Barbuda', 'AG'],
            ['ARG', 'Argentina', 'AR'],
            ['ARM', 'Armenia', 'AM'],
            ['ABW', 'Aruba', 'AW'],
            ['AUS', 'Australia', 'AU'],
            ['AUT', 'Austria', 'AT'],
            ['AZE', 'Azerbaijan', 'AZ'],
            ['BHS', 'Bahamas', 'BS'],
            ['BHR', 'Bahrain', 'BH'],
            ['BGD', 'Bangladesh', 'BD'],
            ['BRB', 'Barbados', 'BB'],
            ['BLR', 'Belarus', 'BY'],
            ['BEL', 'Belgium', 'BE'],
            ['BLZ', 'Belize', 'BZ'],
            ['BEN', 'Benin', 'BJ'],
            ['BMU', 'Bermuda', 'BM'],
            ['BTN', 'Bhutan', 'BT'],
            ['BOL', 'Bolivia', 'BO'],
            ['BES', 'Bonaire', 'BQ'],
            ['BIH', 'Bosnia and Herzegovina', 'BA'],
            ['BWA', 'Botswana', 'BW'],
            ['BVT', 'Bouvet Island', 'BV'],
            ['BRA', 'Brazil', 'BR'],
            ['IOT', 'British Indian Ocean Territory', 'IO'],
            ['VGB', 'British Virgin Islands', 'VG'],
            ['BRN', 'Brunei', 'BN'],
            ['BGR', 'Bulgaria', 'BG'],
            ['BFA', 'Burkina Faso', 'BF'],
            ['BDI', 'Burundi', 'BI'],
            ['KHM', 'Cambodia', 'KH'],
            ['CMR', 'Cameroon', 'CM'],
            ['CAN', 'Canada', 'CA'],
            ['CPV', 'Cape Verde', 'CV'],
            ['CYM', 'Cayman Islands', 'KY'],
            ['CAF', 'Central African Republic', 'CF'],
            ['TCD', 'Chad', 'TD'],
            ['CHL', 'Chile', 'CL'],
            ['CHN', 'China', 'CN'],
            ['CXR', 'Christmas Island', 'CX'],
            ['CCK', 'Cocos [Keeling] Islands', 'CC'],
            ['COL', 'Colombia', 'CO'],
            ['COM', 'Comoros', 'KM'],
            ['COK', 'Cook Islands', 'CK'],
            ['CRI', 'Costa Rica', 'CR'],
            ['HRV', 'Croatia', 'HR'],
            ['CUB', 'Cuba', 'CU'],
            ['CUW', 'Curacao', 'CW'],
            ['CYP', 'Cyprus', 'CY'],
            ['CZE', 'Czech Republic', 'CZ'],
            ['COD', 'Democratic Republic of the Congo', 'CD'],
            ['DNK', 'Denmark', 'DK'],
            ['DJI', 'Djibouti', 'DJ'],
            ['DMA', 'Dominica', 'DM'],
            ['DOM', 'Dominican Republic', 'DO'],
            ['TLS', 'East Timor', 'TL'],
            ['ECU', 'Ecuador', 'EC'],
            ['EGY', 'Egypt', 'EG'],
            ['SLV', 'El Salvador', 'SV'],
            ['GNQ', 'Equatorial Guinea', 'GQ'],
            ['ERI', 'Eritrea', 'ER'],
            ['EST', 'Estonia', 'EE'],
            ['ETH', 'Ethiopia', 'ET'],
            ['FLK', 'Falkland Islands', 'FK'],
            ['FRO', 'Faroe Islands', 'FO'],
            ['FJI', 'Fiji', 'FJ'],
            ['FIN', 'Finland', 'FI'],
            ['FRA', 'France', 'FR'],
            ['GUF', 'French Guiana', 'GF'],
            ['PYF', 'French Polynesia', 'PF'],
            ['ATF', 'French Southern Territories', 'TF'],
            ['GAB', 'Gabon', 'GA'],
            ['GMB', 'Gambia', 'GM'],
            ['GEO', 'Georgia', 'GE'],
            ['DEU', 'Germany', 'DE'],
            ['GHA', 'Ghana', 'GH'],
            ['GIB', 'Gibraltar', 'GI'],
            ['GRC', 'Greece', 'GR'],
            ['GRL', 'Greenland', 'GL'],
            ['GRD', 'Grenada', 'GD'],
            ['GLP', 'Guadeloupe', 'GP'],
            ['GUM', 'Guam', 'GU'],
            ['GTM', 'Guatemala', 'GT'],
            ['GGY', 'Guernsey', 'GG'],
            ['GIN', 'Guinea', 'GN'],
            ['GNB', 'Guinea-Bissau', 'GW'],
            ['GUY', 'Guyana', 'GY'],
            ['HTI', 'Haiti', 'HT'],
            ['HMD', 'Heard Island and McDonald Islands', 'HM'],
            ['HND', 'Honduras', 'HN'],
            ['HKG', 'Hong Kong', 'HK'],
            ['HUN', 'Hungary', 'HU'],
            ['ISL', 'Iceland', 'IS'],
            ['IND', 'India', 'IN'],
            ['IDN', 'Indonesia', 'ID'],
            ['IRN', 'Iran', 'IR'],
            ['IRQ', 'Iraq', 'IQ'],
            ['IRL', 'Ireland', 'IE'],
            ['IMN', 'Isle of Man', 'IM'],
            ['ISR', 'Israel', 'IL'],
            ['ITA', 'Italy', 'IT'],
            ['CIV', 'Ivory Coast', 'CI'],
            ['JAM', 'Jamaica', 'JM'],
            ['JPN', 'Japan', 'JP'],
            ['JEY', 'Jersey', 'JE'],
            ['JOR', 'Jordan', 'JO'],
            ['KAZ', 'Kazakhstan', 'KZ'],
            ['KEN', 'Kenya', 'KE'],
            ['KIR', 'Kiribati', 'KI'],
            ['XKX', 'Kosovo', 'XK'],
            ['KWT', 'Kuwait', 'KW'],
            ['KGZ', 'Kyrgyzstan', 'KG'],
            ['LAO', 'Laos', 'LA'],
            ['LVA', 'Latvia', 'LV'],
            ['LBN', 'Lebanon', 'LB'],
            ['LSO', 'Lesotho', 'LS'],
            ['LBR', 'Liberia', 'LR'],
            ['LBY', 'Libya', 'LY'],
            ['LIE', 'Liechtenstein', 'LI'],
            ['LTU', 'Lithuania', 'LT'],
            ['LUX', 'Luxembourg', 'LU'],
            ['MAC', 'Macao', 'MO'],
            ['MKD', 'Macedonia', 'MK'],
            ['MDG', 'Madagascar', 'MG'],
            ['MWI', 'Malawi', 'MW'],
            ['MYS', 'Malaysia', 'MY'],
            ['MDV', 'Maldives', 'MV'],
            ['MLI', 'Mali', 'ML'],
            ['MLT', 'Malta', 'MT'],
            ['MHL', 'Marshall Islands', 'MH'],
            ['MTQ', 'Martinique', 'MQ'],
            ['MRT', 'Mauritania', 'MR'],
            ['MUS', 'Mauritius', 'MU'],
            ['MYT', 'Mayotte', 'YT'],
            ['MEX', 'Mexico', 'MX'],
            ['FSM', 'Micronesia', 'FM'],
            ['MDA', 'Moldova', 'MD'],
            ['MCO', 'Monaco', 'MC'],
            ['MNG', 'Mongolia', 'MN'],
            ['MNE', 'Montenegro', 'ME'],
            ['MSR', 'Montserrat', 'MS'],
            ['MAR', 'Morocco', 'MA'],
            ['MOZ', 'Mozambique', 'MZ'],
            ['MMR', 'Myanmar [Burma]', 'MM'],
            ['NAM', 'Namibia', 'NA'],
            ['NRU', 'Nauru', 'NR'],
            ['NPL', 'Nepal', 'NP'],
            ['NLD', 'Netherlands', 'NL'],
            ['NCL', 'New Caledonia', 'NC'],
            ['NZL', 'New Zealand', 'NZ'],
            ['NIC', 'Nicaragua', 'NI'],
            ['NER', 'Niger', 'NE'],
            ['NGA', 'Nigeria', 'NG'],
            ['NIU', 'Niue', 'NU'],
            ['NFK', 'Norfolk Island', 'NF'],
            ['PRK', 'North Korea', 'KP'],
            ['MNP', 'Northern Mariana Islands', 'MP'],
            ['NOR', 'Norway', 'NO'],
            ['OMN', 'Oman', 'OM'],
            ['PAK', 'Pakistan', 'PK'],
            ['PLW', 'Palau', 'PW'],
            ['PSE', 'Palestine', 'PS'],
            ['PAN', 'Panama', 'PA'],
            ['PNG', 'Papua New Guinea', 'PG'],
            ['PRY', 'Paraguay', 'PY'],
            ['PER', 'Peru', 'PE'],
            ['PHL', 'Philippines', 'PH'],
            ['PCN', 'Pitcairn Islands', 'PN'],
            ['POL', 'Poland', 'PL'],
            ['PRT', 'Portugal', 'PT'],
            ['PRI', 'Puerto Rico', 'PR'],
            ['QAT', 'Qatar', 'QA'],
            ['COG', 'Republic of the Congo', 'CG'],
            ['REU', 'Réunion', 'RE'],
            ['ROU', 'Romania', 'RO'],
            ['RUS', 'Russia', 'RU'],
            ['RWA', 'Rwanda', 'RW'],
            ['BLM', 'Saint Barthélemy', 'BL'],
            ['SHN', 'Saint Helena', 'SH'],
            ['KNA', 'Saint Kitts and Nevis', 'KN'],
            ['LCA', 'Saint Lucia', 'LC'],
            ['MAF', 'Saint Martin', 'MF'],
            ['SPM', 'Saint Pierre and Miquelon', 'PM'],
            ['VCT', 'Saint Vincent and the Grenadines', 'VC'],
            ['WSM', 'Samoa', 'WS'],
            ['SMR', 'San Marino', 'SM'],
            ['STP', 'São Tomé and Príncipe', 'ST'],
            ['SAU', 'Saudi Arabia', 'SA'],
            ['SEN', 'Senegal', 'SN'],
            ['SRB', 'Serbia', 'RS'],
            ['SYC', 'Seychelles', 'SC'],
            ['SLE', 'Sierra Leone', 'SL'],
            ['SGP', 'Singapore', 'SG'],
            ['SXM', 'Sint Maarten', 'SX'],
            ['SVK', 'Slovakia', 'SK'],
            ['SVN', 'Slovenia', 'SI'],
            ['SLB', 'Solomon Islands', 'SB'],
            ['SOM', 'Somalia', 'SO'],
            ['ZAF', 'South Africa', 'ZA'],
            ['SGS', 'South Georgia and the South Sandwich Islands', 'GS'],
            ['KOR', 'South Korea', 'KR'],
            ['SSD', 'South Sudan', 'SS'],
            ['ESP', 'Spain', 'ES'],
            ['LKA', 'Sri Lanka', 'LK'],
            ['SDN', 'Sudan', 'SD'],
            ['SUR', 'Suriname', 'SR'],
            ['SJM', 'Svalbard and Jan Mayen', 'SJ'],
            ['SWZ', 'Swaziland', 'SZ'],
            ['SWE', 'Sweden', 'SE'],
            ['CHE', 'Switzerland', 'CH'],
            ['SYR', 'Syria', 'SY'],
            ['TWN', 'Taiwan', 'TW'],
            ['TJK', 'Tajikistan', 'TJ'],
            ['TZA', 'Tanzania', 'TZ'],
            ['THA', 'Thailand', 'TH'],
            ['TGO', 'Togo', 'TG'],
            ['TKL', 'Tokelau', 'TK'],
            ['TON', 'Tonga', 'TO'],
            ['TTO', 'Trinidad and Tobago', 'TT'],
            ['TUN', 'Tunisia', 'TN'],
            ['TUR', 'Turkey', 'TR'],
            ['TKM', 'Turkmenistan', 'TM'],
            ['TCA', 'Turks and Caicos Islands', 'TC'],
            ['TUV', 'Tuvalu', 'TV'],
            ['UMI', 'U.S. Minor Outlying Islands', 'UM'],
            ['VIR', 'U.S. Virgin Islands', 'VI'],
            ['UGA', 'Uganda', 'UG'],
            ['UKR', 'Ukraine', 'UA'],
            ['ARE', 'United Arab Emirates', 'AE'],
            ['GBR', 'United Kingdom', 'GB'],
            ['USA', 'United States', 'US'],
            ['URY', 'Uruguay', 'UY'],
            ['UZB', 'Uzbekistan', 'UZ'],
            ['VUT', 'Vanuatu', 'VU'],
            ['VAT', 'Vatican City', 'VA'],
            ['VEN', 'Venezuela', 'VE'],
            ['VNM', 'Vietnam', 'VN'],
            ['WLF', 'Wallis and Futuna', 'WF'],
            ['ESH', 'Western Sahara', 'EH'],
            ['YEM', 'Yemen', 'YE'],
            ['ZMB', 'Zambia', 'ZM'],
            ['ZWE', 'Zimbabwe', 'ZW']
        ];

        /** @var array $country */
        foreach($countries as $country){
            /** @var Country $country */
            $c = new Country();
            $c->setCountryCode($country[0]);
            $c->setCountryName($country[1]);
            $c->setCode($country[2]);
            $c->save();
        }
    }

    /**
     * Insert all category entries
     */
    private function seedCategories(): void
    {
        /** @var array $categories */
        $categories = [
            ['Science & Technology', 'science-technology', 'fa-flask'],
            ['Howto & Style', 'howto-stlye', 'fa-wrench'],
            ['Comedy', 'comedy', 'fa-theater-masks'],
            ['People & Blogs', 'people-blogs', 'fa-comment-alt'],
            ['Gaming', 'gaming', 'fa-hat-wizard'],
            ['Travel & Events', 'travel-events', 'fa-plane'],
            ['Sports', 'sports', 'fa-quidditch'],
            ['Pets & Animals', 'pets-animals', 'fa-paw'],
            ['Music', 'music', 'fa-music'],
            ['Autos & Vehicles', 'auto-vehicles', 'fa-car'],
            ['Film & Animation', 'film-animation', 'fa-film']
        ];

        /** @var array $category */
        foreach($categories as $category){
            /** @var Category $category */
            $c = new Category();
            $c->setTitle($category[0]);
            $c->setSlug($category[1]);
            $c->setIcon($category[2]);
            $c->save();
        }
    }
}
