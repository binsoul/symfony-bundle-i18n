<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\DataFixtures;

use BinSoul\Symfony\Bundle\I18n\Entity\CountryEntity;
use BinSoul\Symfony\Bundle\I18n\Repository\ContinentRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\ObjectManager;

class LoadCountries extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    /**
     * @var mixed[][]
     */
    private static $rows = [
        [1, 3, 'Afghanistan', 'AF', 'AFG', '004', 'AFG', 0.000000, 0.000000],
        [2, 4, 'Albania', 'AL', 'ALB', '008', 'AL', 0.000000, 0.000000],
        [3, 1, 'Algeria', 'DZ', 'DZA', '012', 'DZ', 0.000000, 0.000000],
        [4, 6, 'American Samoa', 'AS', 'ASM', '016', null, 0.000000, 0.000000],
        [5, 4, 'Andorra', 'AD', 'AND', '020', 'AND', 0.000000, 0.000000],
        [6, 1, 'Angola', 'AO', 'AGO', '024', 'ANG', 0.000000, 0.000000],
        [7, 5, 'Anguilla', 'AI', 'AIA', '660', 'AXA', 0.000000, 0.000000],
        [8, 2, 'Antarctica', 'AQ', 'ATA', '010', null, 0.000000, 0.000000],
        [9, 5, 'Antigua and Barbuda', 'AG', 'ATG', '028', 'AG', 0.000000, 0.000000],
        [10, 7, 'Argentina', 'AR', 'ARG', '032', 'RA', 0.000000, 0.000000],
        [11, 3, 'Armenia', 'AM', 'ARM', '051', 'AM', 0.000000, 0.000000],
        [12, 5, 'Aruba', 'AW', 'ABW', '533', 'ARU', 0.000000, 0.000000],
        [13, 6, 'Australia', 'AU', 'AUS', '036', 'AUS', 0.000000, 0.000000],
        [14, 4, 'Österreich', 'AT', 'AUT', '040', 'A', 14.550072, 47.516231],
        [15, 3, 'Azerbaijan', 'AZ', 'AZE', '031', 'AZ', 0.000000, 0.000000],
        [16, 5, 'Bahamas', 'BS', 'BHS', '044', 'BS', 0.000000, 0.000000],
        [17, 3, 'Bahrain', 'BH', 'BHR', '048', 'BRN', 0.000000, 0.000000],
        [18, 3, 'Bangladesh', 'BD', 'BGD', '050', 'BD', 0.000000, 0.000000],
        [19, 5, 'Barbados', 'BB', 'BRB', '052', 'BDS', 0.000000, 0.000000],
        [20, 4, 'Belarus', 'BY', 'BLR', '112', 'BY', 0.000000, 0.000000],
        [21, 4, 'Belgien', 'BE', 'BEL', '056', 'B', 0.000000, 0.000000],
        [22, 5, 'Belize', 'BZ', 'BLZ', '084', 'BZ', 0.000000, 0.000000],
        [23, 1, 'Benin', 'BJ', 'BEN', '204', 'BJ', 0.000000, 0.000000],
        [24, 5, 'Bermuda', 'BM', 'BMU', '060', null, 0.000000, 0.000000],
        [25, 3, 'Bhutan', 'BT', 'BTN', '064', 'BHT', 0.000000, 0.000000],
        [26, 7, 'Bolivia', 'BO', 'BOL', '068', 'BOL', 0.000000, 0.000000],
        [27, 4, 'Bosnia and Herzegowina', 'BA', 'BIH', '070', 'BIH', 0.000000, 0.000000],
        [28, 1, 'Botswana', 'BW', 'BWA', '072', 'RB', 0.000000, 0.000000],
        [29, 2, 'Bouvet Island', 'BV', 'BVT', '074', null, 0.000000, 0.000000],
        [30, 7, 'Brazil', 'BR', 'BRA', '076', 'BR', 0.000000, 0.000000],
        [31, 3, 'British Indian Ocean Territory', 'IO', 'IOT', '086', null, 0.000000, 0.000000],
        [32, 3, 'Brunei Darussalam', 'BN', 'BRN', '096', 'BRU', 0.000000, 0.000000],
        [33, 4, 'Bulgaria', 'BG', 'BGR', '100', 'BG', 0.000000, 0.000000],
        [34, 1, 'Burkina Faso', 'BF', 'BFA', '854', 'BF', 0.000000, 0.000000],
        [35, 1, 'Burundi', 'BI', 'BDI', '108', 'RU', 0.000000, 0.000000],
        [36, 3, 'Cambodia', 'KH', 'KHM', '116', 'K', 0.000000, 0.000000],
        [37, 1, 'Cameroon', 'CM', 'CMR', '120', 'CAM', 0.000000, 0.000000],
        [38, 5, 'Canada', 'CA', 'CAN', '124', 'CDN', 0.000000, 0.000000],
        [39, 1, 'Cape Verde', 'CV', 'CPV', '132', 'CV', 0.000000, 0.000000],
        [40, 5, 'Cayman Islands', 'KY', 'CYM', '136', null, 0.000000, 0.000000],
        [41, 1, 'Central African Republic', 'CF', 'CAF', '140', 'RCA', 0.000000, 0.000000],
        [42, 1, 'Chad', 'TD', 'TCD', '148', 'TD', 0.000000, 0.000000],
        [43, 7, 'Chile', 'CL', 'CHL', '152', 'RCH', 0.000000, 0.000000],
        [44, 3, 'China', 'CN', 'CHN', '156', 'CHN', 0.000000, 0.000000],
        [45, 3, 'Christmas Island', 'CX', 'CXR', '162', null, 0.000000, 0.000000],
        [46, 3, 'Cocos [Keeling] Islands', 'CC', 'CCK', '166', null, 0.000000, 0.000000],
        [47, 7, 'Colombia', 'CO', 'COL', '170', 'CO', 0.000000, 0.000000],
        [48, 1, 'Comoros', 'KM', 'COM', '174', 'COM', 0.000000, 0.000000],
        [49, 1, 'Congo', 'CG', 'COG', '178', 'RCB', 0.000000, 0.000000],
        [50, 6, 'Cook Islands', 'CK', 'COK', '184', null, 0.000000, 0.000000],
        [51, 5, 'Costa Rica', 'CR', 'CRI', '188', 'CR', 0.000000, 0.000000],
        [52, 1, 'Cote D\'Ivoire', 'CI', 'CIV', '384', 'CI', 0.000000, 0.000000],
        [53, 4, 'Kroatien', 'HR', 'HRV', '191', 'HR', 0.000000, 0.000000],
        [54, 5, 'Cuba', 'CU', 'CUB', '192', 'C', 0.000000, 0.000000],
        [55, 3, 'Zypern', 'CY', 'CYP', '196', 'CY', 0.000000, 0.000000],
        [56, 4, 'Tschechien', 'CZ', 'CZE', '203', 'CZ', 0.000000, 0.000000],
        [57, 4, 'Dänemark', 'DK', 'DNK', '208', 'DK', 0.000000, 0.000000],
        [58, 1, 'Djibouti', 'DJ', 'DJI', '262', 'DJI', 0.000000, 0.000000],
        [59, 5, 'Dominica', 'DM', 'DMA', '212', 'WD', 0.000000, 0.000000],
        [60, 5, 'Dominican Republic', 'DO', 'DOM', '214', 'DOM', 0.000000, 0.000000],
        [61, 3, 'East Timor', 'TL', 'TMP', '626', 'TL', 0.000000, 0.000000],
        [62, 7, 'Ecuador', 'EC', 'ECU', '218', 'EC', 0.000000, 0.000000],
        [63, 1, 'Egypt', 'EG', 'EGY', '818', 'ET', 0.000000, 0.000000],
        [64, 5, 'El Salvador', 'SV', 'SLV', '222', 'ES', 0.000000, 0.000000],
        [65, 1, 'Equatorial Guinea', 'GQ', 'GNQ', '226', 'GQ', 0.000000, 0.000000],
        [66, 1, 'Eritrea', 'ER', 'ERI', '232', 'ER', 0.000000, 0.000000],
        [67, 4, 'Estland', 'EE', 'EST', '233', 'EST', 0.000000, 0.000000],
        [68, 1, 'Ethiopia', 'ET', 'ETH', '231', 'ETH', 0.000000, 0.000000],
        [69, 7, 'Falkland Islands [Malvinas]', 'FK', 'FLK', '238', null, 0.000000, 0.000000],
        [70, 4, 'Faroe Islands', 'FO', 'FRO', '234', 'FO', 0.000000, 0.000000],
        [71, 6, 'Fiji', 'FJ', 'FJI', '242', 'FJI', 0.000000, 0.000000],
        [72, 4, 'Finnland', 'FI', 'FIN', '246', 'FIN', 0.000000, 0.000000],
        [73, 4, 'Frankreich', 'FR', 'FRA', '250', 'F', 0.000000, 0.000000],
        [75, 7, 'Französisch-Guayana', 'GF', 'GUF', '254', null, 0.000000, 0.000000],
        [76, 6, 'Französisch-Polynesien', 'PF', 'PYF', '258', null, 0.000000, 0.000000],
        [77, 2, 'Französische Süd- und Antarktisgebiete', 'TF', 'ATF', '260', null, 0.000000, 0.000000],
        [78, 1, 'Gabon', 'GA', 'GAB', '266', 'G', 0.000000, 0.000000],
        [79, 1, 'Gambia', 'GM', 'GMB', '270', 'WAG', 0.000000, 0.000000],
        [80, 3, 'Georgia', 'GE', 'GEO', '268', 'GE', 0.000000, 0.000000],
        [81, 4, 'Deutschland', 'DE', 'DEU', '276', 'D', 10.447683, 51.163375],
        [82, 1, 'Ghana', 'GH', 'GHA', '288', 'GH', 0.000000, 0.000000],
        [83, 4, 'Gibraltar', 'GI', 'GIB', '292', 'GBZ', 0.000000, 0.000000],
        [84, 4, 'Griechenland', 'GR', 'GRC', '300', 'GR', 0.000000, 0.000000],
        [85, 5, 'Greenland', 'GL', 'GRL', '304', 'KN', 0.000000, 0.000000],
        [86, 5, 'Grenada', 'GD', 'GRD', '308', 'WG', 0.000000, 0.000000],
        [87, 5, 'Guadeloupe', 'GP', 'GLP', '312', null, 0.000000, 0.000000],
        [88, 6, 'Guam', 'GU', 'GUM', '316', null, 0.000000, 0.000000],
        [89, 5, 'Guatemala', 'GT', 'GTM', '320', 'GT', 0.000000, 0.000000],
        [90, 1, 'Guinea', 'GN', 'GIN', '324', 'RG', 0.000000, 0.000000],
        [91, 1, 'Guinea-bissau', 'GW', 'GNB', '624', 'GUB', 0.000000, 0.000000],
        [92, 7, 'Guyana', 'GY', 'GUY', '328', 'GUY', 0.000000, 0.000000],
        [93, 5, 'Haiti', 'HT', 'HTI', '332', 'RH', 0.000000, 0.000000],
        [94, 2, 'Heard and Mc Donald Islands', 'HM', 'HMD', '334', null, 0.000000, 0.000000],
        [95, 5, 'Honduras', 'HN', 'HND', '340', 'HN', 0.000000, 0.000000],
        [96, 3, 'Hong Kong', 'HK', 'HKG', '344', 'HK', 0.000000, 0.000000],
        [97, 4, 'Ungarn', 'HU', 'HUN', '348', 'H', 0.000000, 0.000000],
        [98, 4, 'Iceland', 'IS', 'ISL', '352', 'IS', 0.000000, 0.000000],
        [99, 3, 'India', 'IN', 'IND', '356', 'IND', 0.000000, 0.000000],
        [100, 3, 'Indonesia', 'ID', 'IDN', '360', 'RI', 0.000000, 0.000000],
        [101, 3, 'Iran [Islamic Republic of]', 'IR', 'IRN', '364', 'IR', 0.000000, 0.000000],
        [102, 3, 'Iraq', 'IQ', 'IRQ', '368', 'IRQ', 0.000000, 0.000000],
        [103, 4, 'Irland', 'IE', 'IRL', '372', 'IRL', 0.000000, 0.000000],
        [104, 3, 'Israel', 'IL', 'ISR', '376', 'IL', 0.000000, 0.000000],
        [105, 4, 'Italien', 'IT', 'ITA', '380', 'I', 0.000000, 0.000000],
        [106, 5, 'Jamaica', 'JM', 'JAM', '388', 'JA', 0.000000, 0.000000],
        [107, 3, 'Japan', 'JP', 'JPN', '392', 'J', 0.000000, 0.000000],
        [108, 3, 'Jordan', 'JO', 'JOR', '400', 'JOR', 0.000000, 0.000000],
        [109, 3, 'Kazakhstan', 'KZ', 'KAZ', '398', 'KZ', 0.000000, 0.000000],
        [110, 1, 'Kenya', 'KE', 'KEN', '404', 'EAK', 0.000000, 0.000000],
        [111, 6, 'Kiribati', 'KI', 'KIR', '296', 'KIR', 0.000000, 0.000000],
        [112, 3, 'Korea, Democratic People\'s Republic of', 'KP', 'PRK', '408', 'KP', 0.000000, 0.000000],
        [113, 3, 'Korea, Republic of', 'KR', 'KOR', '410', 'ROK', 0.000000, 0.000000],
        [114, 3, 'Kuwait', 'KW', 'KWT', '414', 'KWT', 0.000000, 0.000000],
        [115, 3, 'Kyrgyzstan', 'KG', 'KGZ', '417', 'KS', 0.000000, 0.000000],
        [116, 3, 'Lao People\'s Democratic Republic', 'LA', 'LAO', '418', 'LAO', 0.000000, 0.000000],
        [117, 4, 'Lettland', 'LV', 'LVA', '428', 'LV', 0.000000, 0.000000],
        [118, 3, 'Lebanon', 'LB', 'LBN', '422', 'RL', 0.000000, 0.000000],
        [119, 1, 'Lesotho', 'LS', 'LSO', '426', 'LS', 0.000000, 0.000000],
        [120, 1, 'Liberia', 'LR', 'LBR', '430', 'LB', 0.000000, 0.000000],
        [121, 1, 'Libyan Arab Jamahiriya', 'LY', 'LBY', '434', 'LAR', 0.000000, 0.000000],
        [122, 4, 'Liechtenstein', 'LI', 'LIE', '438', 'FL', 0.000000, 0.000000],
        [123, 4, 'Litauen', 'LT', 'LTU', '440', 'LT', 0.000000, 0.000000],
        [124, 4, 'Luxemburg', 'LU', 'LUX', '442', 'L', 0.000000, 0.000000],
        [125, 3, 'Macau', 'MO', 'MAC', '446', null, 0.000000, 0.000000],
        [126, 4, 'Macedonia, The Former Yugoslav Republic of', 'MK', 'MKD', '807', 'MK', 0.000000, 0.000000],
        [127, 1, 'Madagascar', 'MG', 'MDG', '450', 'RM', 0.000000, 0.000000],
        [128, 1, 'Malawi', 'MW', 'MWI', '454', 'MW', 0.000000, 0.000000],
        [129, 3, 'Malaysia', 'MY', 'MYS', '458', 'MAL', 0.000000, 0.000000],
        [130, 3, 'Maldives', 'MV', 'MDV', '462', 'MV', 0.000000, 0.000000],
        [131, 1, 'Mali', 'ML', 'MLI', '466', 'RMM', 0.000000, 0.000000],
        [132, 4, 'Malta', 'MT', 'MLT', '470', 'M', 0.000000, 0.000000],
        [133, 6, 'Marshall Islands', 'MH', 'MHL', '584', 'MH', 0.000000, 0.000000],
        [134, 5, 'Martinique', 'MQ', 'MTQ', '474', null, 0.000000, 0.000000],
        [135, 1, 'Mauritania', 'MR', 'MRT', '478', 'RIM', 0.000000, 0.000000],
        [136, 1, 'Mauritius', 'MU', 'MUS', '480', 'MS', 0.000000, 0.000000],
        [137, 1, 'Mayotte', 'YT', 'MYT', '175', null, 0.000000, 0.000000],
        [138, 5, 'Mexico', 'MX', 'MEX', '484', 'MEX', 0.000000, 0.000000],
        [139, 6, 'Micronesia, Federated States of', 'FM', 'FSM', '583', 'FSM', 0.000000, 0.000000],
        [140, 4, 'Moldova, Republic of', 'MD', 'MDA', '498', 'MD', 0.000000, 0.000000],
        [141, 4, 'Monaco', 'MC', 'MCO', '492', 'MC', 0.000000, 0.000000],
        [142, 3, 'Mongolia', 'MN', 'MNG', '496', 'MGL', 0.000000, 0.000000],
        [143, 5, 'Montserrat', 'MS', 'MSR', '500', null, 0.000000, 0.000000],
        [144, 1, 'Morocco', 'MA', 'MAR', '504', 'MA', 0.000000, 0.000000],
        [145, 1, 'Mozambique', 'MZ', 'MOZ', '508', 'MOC', 0.000000, 0.000000],
        [146, 3, 'Myanmar', 'MM', 'MMR', '104', 'MYA', 0.000000, 0.000000],
        [147, 1, 'Namibia', 'NA', 'NAM', '516', 'NAM', 0.000000, 0.000000],
        [148, 6, 'Nauru', 'NR', 'NRU', '520', 'NAU', 0.000000, 0.000000],
        [149, 3, 'Nepal', 'NP', 'NPL', '524', 'NEP', 0.000000, 0.000000],
        [150, 4, 'Niederlande', 'NL', 'NLD', '528', 'NL', 0.000000, 0.000000],
        [151, 5, 'Netherlands Antilles', 'AN', 'ANT', null, 'NA', 0.000000, 0.000000],
        [152, 6, 'Neukaledonien', 'NC', 'NCL', '540', 'NCL', 0.000000, 0.000000],
        [153, 6, 'New Zealand', 'NZ', 'NZL', '554', 'NZ', 0.000000, 0.000000],
        [154, 5, 'Nicaragua', 'NI', 'NIC', '558', 'NIC', 0.000000, 0.000000],
        [155, 1, 'Niger', 'NE', 'NER', '562', 'RN', 0.000000, 0.000000],
        [156, 1, 'Nigeria', 'NG', 'NGA', '566', 'NGR', 0.000000, 0.000000],
        [157, 6, 'Niue', 'NU', 'NIU', '570', null, 0.000000, 0.000000],
        [158, 6, 'Norfolk Island', 'NF', 'NFK', '574', null, 0.000000, 0.000000],
        [159, 6, 'Northern Mariana Islands', 'MP', 'MNP', '580', null, 0.000000, 0.000000],
        [160, 4, 'Norwegen', 'NO', 'NOR', '578', 'N', 0.000000, 0.000000],
        [161, 3, 'Oman', 'OM', 'OMN', '512', 'OM', 0.000000, 0.000000],
        [162, 3, 'Pakistan', 'PK', 'PAK', '586', 'PK', 0.000000, 0.000000],
        [163, 6, 'Palau', 'PW', 'PLW', '585', 'PAL', 0.000000, 0.000000],
        [164, 5, 'Panama', 'PA', 'PAN', '591', 'PA', 0.000000, 0.000000],
        [165, 6, 'Papua New Guinea', 'PG', 'PNG', '598', 'PNG', 0.000000, 0.000000],
        [166, 7, 'Paraguay', 'PY', 'PRY', '600', 'PY', 0.000000, 0.000000],
        [167, 7, 'Peru', 'PE', 'PER', '604', 'PE', 0.000000, 0.000000],
        [168, 3, 'Philippines', 'PH', 'PHL', '608', 'RP', 0.000000, 0.000000],
        [169, 6, 'Pitcairn', 'PN', 'PCN', '612', null, 0.000000, 0.000000],
        [170, 4, 'Polen', 'PL', 'POL', '616', 'PL', 0.000000, 0.000000],
        [171, 4, 'Portugal', 'PT', 'PRT', '620', 'P', 0.000000, 0.000000],
        [172, 5, 'Puerto Rico', 'PR', 'PRI', '630', 'PRI', 0.000000, 0.000000],
        [173, 3, 'Qatar', 'QA', 'QAT', '634', 'Q', 0.000000, 0.000000],
        [174, 1, 'Réunion', 'RE', 'REU', '638', null, 0.000000, 0.000000],
        [175, 4, 'Rumänien', 'RO', 'ROM', '642', 'RUM', 0.000000, 0.000000],
        [176, 4, 'Russian Federation', 'RU', 'RUS', '643', 'RUS', 0.000000, 0.000000],
        [177, 1, 'Rwanda', 'RW', 'RWA', '646', 'RWA', 0.000000, 0.000000],
        [178, 5, 'Saint Kitts and Nevis', 'KN', 'KNA', '659', 'KAN', 0.000000, 0.000000],
        [179, 5, 'Saint Lucia', 'LC', 'LCA', '662', 'WL', 0.000000, 0.000000],
        [180, 5, 'Saint Vincent and the Grenadines', 'VC', 'VCT', '670', 'WV', 0.000000, 0.000000],
        [181, 6, 'Samoa', 'WS', 'WSM', '882', 'WS', 0.000000, 0.000000],
        [182, 4, 'San Marino', 'SM', 'SMR', '674', 'RSM', 0.000000, 0.000000],
        [183, 1, 'Sao Tome and Principe', 'ST', 'STP', '678', 'STP', 0.000000, 0.000000],
        [184, 3, 'Saudi Arabia', 'SA', 'SAU', '682', 'KSA', 0.000000, 0.000000],
        [185, 1, 'Senegal', 'SN', 'SEN', '686', 'SN', 0.000000, 0.000000],
        [186, 1, 'Seychelles', 'SC', 'SYC', '690', 'SY', 0.000000, 0.000000],
        [187, 1, 'Sierra Leone', 'SL', 'SLE', '694', 'WAL', 0.000000, 0.000000],
        [188, 3, 'Singapore', 'SG', 'SGP', '702', 'SGP', 0.000000, 0.000000],
        [189, 4, 'Slowakei', 'SK', 'SVK', '703', 'SK', 0.000000, 0.000000],
        [190, 4, 'Slowenien', 'SI', 'SVN', '705', 'SLO', 0.000000, 0.000000],
        [191, 6, 'Solomon Islands', 'SB', 'SLB', '090', 'SOL', 0.000000, 0.000000],
        [192, 1, 'Somalia', 'SO', 'SOM', '706', 'SO', 0.000000, 0.000000],
        [193, 1, 'South Africa', 'ZA', 'ZAF', '710', 'ZA', 0.000000, 0.000000],
        [194, 2, 'South Georgia and the South Sandwich Islands', 'GS', 'SGS', '239', null, 0.000000, 0.000000],
        [195, 4, 'Spanien', 'ES', 'ESP', '724', 'E', 0.000000, 0.000000],
        [196, 3, 'Sri Lanka', 'LK', 'LKA', '144', 'CL', 0.000000, 0.000000],
        [197, 1, 'St. Helena', 'SH', 'SHN', '654', null, 0.000000, 0.000000],
        [198, 5, 'Saint-Pierre und Miquelon', 'PM', 'SPM', '666', null, 0.000000, 0.000000],
        [199, 1, 'Sudan', 'SD', 'SDN', '729', 'SUD', 0.000000, 0.000000],
        [200, 7, 'Suriname', 'SR', 'SUR', '740', 'SME', 0.000000, 0.000000],
        [201, 4, 'Svalbard and Jan Mayen Islands', 'SJ', 'SJM', '744', null, 0.000000, 0.000000],
        [202, 1, 'Swaziland', 'SZ', 'SWZ', '748', 'SD', 0.000000, 0.000000],
        [203, 4, 'Schweden', 'SE', 'SWE', '752', 'S', 0.000000, 0.000000],
        [204, 4, 'Schweiz', 'CH', 'CHE', '756', 'CH', 8.227512, 46.818188],
        [205, 3, 'Syrian Arab Republic', 'SY', 'SYR', '760', 'SYR', 0.000000, 0.000000],
        [206, 3, 'Taiwan', 'TW', 'TWN', '158', 'RC', 0.000000, 0.000000],
        [207, 3, 'Tajikistan', 'TJ', 'TJK', '762', 'TJ', 0.000000, 0.000000],
        [208, 1, 'Tanzania, United Republic of', 'TZ', 'TZA', '834', 'EAT', 0.000000, 0.000000],
        [209, 3, 'Thailand', 'TH', 'THA', '764', 'T', 0.000000, 0.000000],
        [210, 1, 'Togo', 'TG', 'TGO', '768', 'TG', 0.000000, 0.000000],
        [211, 6, 'Tokelau', 'TK', 'TKL', '772', null, 0.000000, 0.000000],
        [212, 6, 'Tonga', 'TO', 'TON', '776', 'TON', 0.000000, 0.000000],
        [213, 5, 'Trinidad and Tobago', 'TT', 'TTO', '780', 'TT', 0.000000, 0.000000],
        [214, 1, 'Tunisia', 'TN', 'TUN', '788', 'TN', 0.000000, 0.000000],
        [215, 3, 'Turkey', 'TR', 'TUR', '792', 'TR', 0.000000, 0.000000],
        [216, 3, 'Turkmenistan', 'TM', 'TKM', '795', 'TM', 0.000000, 0.000000],
        [217, 5, 'Turks and Caicos Islands', 'TC', 'TCA', '796', null, 0.000000, 0.000000],
        [218, 6, 'Tuvalu', 'TV', 'TUV', '798', 'TUV', 0.000000, 0.000000],
        [219, 1, 'Uganda', 'UG', 'UGA', '800', 'EAU', 0.000000, 0.000000],
        [220, 4, 'Ukraine', 'UA', 'UKR', '804', 'UA', 0.000000, 0.000000],
        [221, 3, 'United Arab Emirates', 'AE', 'ARE', '784', 'UAE', 0.000000, 0.000000],
        [222, 4, 'United Kingdom', 'GB', 'GBR', '826', 'GBM', 0.000000, 0.000000],
        [223, 5, 'United States', 'US', 'USA', '840', 'USA', 0.000000, 0.000000],
        [224, 6, 'United States Minor Outlying Islands', 'UM', 'UMI', '581', null, 0.000000, 0.000000],
        [225, 7, 'Uruguay', 'UY', 'URY', '858', 'ROU', 0.000000, 0.000000],
        [226, 3, 'Uzbekistan', 'UZ', 'UZB', '860', 'UZ', 0.000000, 0.000000],
        [227, 6, 'Vanuatu', 'VU', 'VUT', '548', 'VAN', 0.000000, 0.000000],
        [228, 4, 'Vatican City State [Holy See]', 'VA', 'VAT', '336', 'V', 0.000000, 0.000000],
        [229, 7, 'Venezuela', 'VE', 'VEN', '862', 'YV', 0.000000, 0.000000],
        [230, 3, 'Viet Nam', 'VN', 'VNM', '704', 'VN', 0.000000, 0.000000],
        [231, 5, 'Virgin Islands [British]', 'VG', 'VGB', '092', null, 0.000000, 0.000000],
        [232, 5, 'Virgin Islands [U.S.]', 'VI', 'VIR', '850', null, 0.000000, 0.000000],
        [233, 6, 'Wallis und Futuna', 'WF', 'WLF', '876', null, 0.000000, 0.000000],
        [234, 1, 'Western Sahara', 'EH', 'ESH', '732', 'WSA', 0.000000, 0.000000],
        [235, 3, 'Yemen', 'YE', 'YEM', '887', 'YEM', 0.000000, 0.000000],
        [237, 1, 'Zaire', 'CD', 'ZAR', '180', 'CGO', 0.000000, 0.000000],
        [238, 1, 'Zambia', 'ZM', 'ZMB', '894', 'Z', 0.000000, 0.000000],
        [239, 1, 'Zimbabwe', 'ZW', 'ZWE', '716', 'ZW', 0.000000, 0.000000],
        [240, 5, 'Saint-Martin', 'MF', 'MAF', '663', null, 0.000000, 0.000000],
        [241, 5, 'Saint-Barthélemy', 'BL', 'BLM', '652', null, 0.000000, 0.000000],
        [242, 3, 'Palästinensische Gebiete', 'PS', 'PSE', '275', 'WB', 0.000000, 0.000000],
        [243, 4, 'Montenegro', 'ME', 'MNE', '499', 'MNE', 0.000000, 0.000000],
        [244, 5, 'Sint Maarten', 'SX', 'SXM', '534', null, 0.000000, 0.000000],
        [245, 5, 'Curaçao', 'CW', 'CUW', '531', null, 0.000000, 0.000000],
        [246, 4, 'Insel Man', 'IM', 'IMN', '833', null, 0.000000, 0.000000],
        [247, 4, 'Guernsey', 'GG', 'GGY', '831', 'GBG', 0.000000, 0.000000],
        [248, 4, 'Jersey', 'JE', 'JEY', '832', 'GBJ', 0.000000, 0.000000],
        [249, 1, 'Südsudan', 'SS', 'SSD', '728', 'SSD', 0.000000, 0.000000],
        [250, 4, 'Åland', 'AX', 'ALA', '248', 'AX', 0.000000, 0.000000],
        [251, 5, 'Bonaire', 'BQ', 'BES', '535', null, 0.000000, 0.000000],
        [252, 4, 'Serbien', 'RS', 'SRB', '688', 'SRB', 0.000000, 0.000000],
        [253, 1, 'Ascension', 'AC', 'ASC', null, null, 0.000000, 0.000000],
        [254, 5, 'Clipperton', 'CP', 'CPT', null, null, 0.000000, 0.000000],
        [255, 6, 'Diego Garcia', 'DG', 'DGA', null, null, 0.000000, 0.000000],
        [256, 1, 'Ceuta, Melilla', 'EA', null, null, null, 0.000000, 0.000000],
        [257, 1, 'Kanarische Inseln', 'IC', null, null, null, 0.000000, 0.000000],
        [258, 6, 'Äußeres Ozeanien', 'QO', null, null, null, 0.000000, 0.000000],
        [259, 1, 'Tristan da Cunha', 'TA', 'TAA', null, null, 0.000000, 0.000000],
    ];

    /**
     * @var ContinentRepository
     */
    private $continentRepository;

    /**
     * Constructs an instance of this class.
     */
    public function __construct(ContinentRepository $continentRepository)
    {
        $this->continentRepository = $continentRepository;
    }

    public function load(ObjectManager $manager): void
    {
        /** @var ClassMetadataInfo $metadata */
        $metadata = $manager->getClassMetadata(CountryEntity::class);
        $metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new AssignedGenerator());

        foreach (self::$rows as $row) {
            $continent = $this->continentRepository->load($row[1]);

            if (! $continent) {
                continue;
            }

            $entity = new CountryEntity($row[0]);
            $entity->setContinent($continent);
            $entity->setIso2($row[3]);
            $entity->setIso3($row[4]);
            $entity->setIsoNumeric($row[5]);
            $entity->setDsit($row[6]);
            $entity->setCenterLongitude($row[7]);
            $entity->setCenterLatitude($row[8]);
            $manager->persist($entity);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [LoadContinents::class];
    }

    public static function getGroups(): array
    {
        return ['binsoul/symfony-bundle-i18n'];
    }
}
