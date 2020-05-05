<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\I18n\DataFixtures;

use BinSoul\Symfony\Bundle\I18n\Entity\LanguageEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\ObjectManager;

class LoadLanguages extends Fixture implements FixtureGroupInterface
{
    /**
     * @var mixed[][]
     */
    private static $rows = [
        [1, 'Abkhaz', 'ab', 'abk', 'ltr'],
        [2, 'Afar', 'aa', 'aar', 'ltr'],
        [3, 'Afrikaans', 'af', 'afr', 'ltr'],
        [4, 'Akan', 'ak', 'aka', 'ltr'],
        [5, 'Albanian', 'sq', 'alb', 'ltr'],
        [6, 'Amharic', 'am', 'amh', 'ltr'],
        [7, 'Arabic', 'ar', 'ara', 'rtl'],
        [8, 'Aragonese', 'an', 'arg', 'ltr'],
        [9, 'Armenian', 'hy', 'arm', 'ltr'],
        [10, 'Assamese', 'as', 'asm', 'ltr'],
        [11, 'Avaric', 'av', 'ava', 'ltr'],
        [12, 'Avestan', 'ae', 'ave', 'ltr'],
        [13, 'Aymara', 'ay', 'aym', 'ltr'],
        [14, 'Azerbaijani', 'az', 'aze', 'ltr'],
        [15, 'Bambara', 'bm', 'bam', 'ltr'],
        [16, 'Bashkir', 'ba', 'bak', 'ltr'],
        [17, 'Basque', 'eu', 'baq', 'ltr'],
        [18, 'Belarusian', 'be', 'bel', 'ltr'],
        [19, 'Bengali', 'bn', 'ben', 'ltr'],
        [20, 'Bihari', 'bh', 'bih', 'ltr'],
        [21, 'Bislama', 'bi', 'bis', 'ltr'],
        [22, 'Bosnian', 'bs', 'bos', 'ltr'],
        [23, 'Breton', 'br', 'bre', 'ltr'],
        [24, 'Bulgarian', 'bg', 'bul', 'ltr'],
        [25, 'Burmese', 'my', 'bur', 'ltr'],
        [26, 'Catalan', 'ca', 'cat', 'ltr'],
        [27, 'Chamorro', 'ch', 'cha', 'ltr'],
        [28, 'Chechen', 'ce', 'che', 'ltr'],
        [29, 'Chichewa', 'ny', 'nya', 'ltr'],
        [30, 'Chinese', 'zh', 'chi', 'ltr'],
        [31, 'Chuvash', 'cv', 'chv', 'ltr'],
        [32, 'Cornish', 'kw', 'cor', 'ltr'],
        [33, 'Corsican', 'co', 'cos', 'ltr'],
        [34, 'Cree', 'cr', 'cre', 'ltr'],
        [35, 'Croatian', 'hr', 'hrv', 'ltr'],
        [36, 'Czech', 'cs', 'cze', 'ltr'],
        [37, 'Danish', 'da', 'dan', 'ltr'],
        [38, 'Divehi', 'dv', 'div', 'rtl'],
        [39, 'Dutch', 'nl', 'dut', 'ltr'],
        [40, 'Dzongkha', 'dz', 'dzo', 'ltr'],
        [41, 'English', 'en', 'eng', 'ltr'],
        [42, 'Esperanto', 'eo', 'epo', 'ltr'],
        [43, 'Estonian', 'et', 'est', 'ltr'],
        [44, 'Ewe', 'ee', 'ewe', 'ltr'],
        [45, 'Faroese', 'fo', 'fao', 'ltr'],
        [46, 'Fijian', 'fj', 'fij', 'ltr'],
        [47, 'Finnish', 'fi', 'fin', 'ltr'],
        [48, 'French', 'fr', 'fre', 'ltr'],
        [49, 'Fula', 'ff', 'ful', 'ltr'],
        [50, 'Gaelic', 'gd', 'gla', 'ltr'],
        [51, 'Galician', 'gl', 'glg', 'ltr'],
        [52, 'Ganda', 'lg', 'lug', 'ltr'],
        [53, 'Georgian', 'ka', 'geo', 'ltr'],
        [54, 'German', 'de', 'ger', 'ltr'],
        [55, 'Gikuyu', 'ki', 'kik', 'ltr'],
        [56, 'Greek', 'el', 'gre', 'ltr'],
        [57, 'Guaraní', 'gn', 'grn', 'ltr'],
        [58, 'Gujarati', 'gu', 'guj', 'ltr'],
        [59, 'Haitian', 'ht', 'hat', 'ltr'],
        [60, 'Hausa', 'ha', 'hau', 'rtl'],
        [61, 'Hebrew', 'he', 'heb', 'rtl'],
        [62, 'Herero', 'hz', 'her', 'ltr'],
        [63, 'Hindi', 'hi', 'hin', 'ltr'],
        [64, 'Hiri Motu', 'ho', 'hmo', 'ltr'],
        [65, 'Hungarian', 'hu', 'hun', 'ltr'],
        [66, 'Icelandic', 'is', 'ice', 'ltr'],
        [67, 'Ido', 'io', 'ido', 'ltr'],
        [68, 'Igbo', 'ig', 'ibo', 'ltr'],
        [69, 'Indonesian', 'id', 'ind', 'ltr'],
        [70, 'Interlingua', 'ia', 'ina', 'ltr'],
        [71, 'Interlingue', 'ie', 'ile', 'ltr'],
        [72, 'Inuktitut', 'iu', 'iku', 'ltr'],
        [73, 'Inupiaq', 'ik', 'ipk', 'ltr'],
        [74, 'Irish', 'ga', 'gle', 'ltr'],
        [75, 'Italian', 'it', 'ita', 'ltr'],
        [76, 'Japanese', 'ja', 'jpn', 'ltr'],
        [77, 'Javanese', 'jv', 'jav', 'ltr'],
        [78, 'Kalaallisut', 'kl', 'kal', 'ltr'],
        [79, 'Kannada', 'kn', 'kan', 'ltr'],
        [80, 'Kanuri', 'kr', 'kau', 'ltr'],
        [81, 'Kashmiri', 'ks', 'kas', 'rtl'],
        [82, 'Kazakh', 'kk', 'kaz', 'ltr'],
        [83, 'Khmer', 'km', 'khm', 'ltr'],
        [84, 'Kinyarwanda', 'rw', 'kin', 'ltr'],
        [85, 'Kirundi', 'rn', 'run', 'ltr'],
        [86, 'Komi', 'kv', 'kom', 'ltr'],
        [87, 'Kongo', 'kg', 'kon', 'ltr'],
        [88, 'Korean', 'ko', 'kor', 'ltr'],
        [89, 'Kurdish', 'ku', 'kur', 'rtl'],
        [90, 'Kwanyama', 'kj', 'kua', 'ltr'],
        [91, 'Kyrgyz', 'ky', 'kir', 'ltr'],
        [92, 'Lao', 'lo', 'lao', 'ltr'],
        [93, 'Latin', 'la', 'lat', 'ltr'],
        [94, 'Latvian', 'lv', 'lav', 'ltr'],
        [95, 'Limburgish', 'li', 'lim', 'ltr'],
        [96, 'Lingala', 'ln', 'lin', 'ltr'],
        [97, 'Lithuanian', 'lt', 'lit', 'ltr'],
        [98, 'Luba-Katanga', 'lu', 'lub', 'ltr'],
        [99, 'Luxembourgish', 'lb', 'ltz', 'ltr'],
        [100, 'M?ori', 'mi', 'mao', 'ltr'],
        [101, 'Macedonian', 'mk', 'mac', 'ltr'],
        [102, 'Malagasy', 'mg', 'mlg', 'ltr'],
        [103, 'Malay', 'ms', 'may', 'ltr'],
        [104, 'Malayalam', 'ml', 'mal', 'ltr'],
        [105, 'Maltese', 'mt', 'mlt', 'ltr'],
        [106, 'Manx', 'gv', 'glv', 'ltr'],
        [107, 'Marathi', 'mr', 'mar', 'ltr'],
        [108, 'Marshallese', 'mh', 'mah', 'ltr'],
        [109, 'Mongolian', 'mn', 'mon', 'ltr'],
        [110, 'Nauru', 'na', 'nau', 'ltr'],
        [111, 'Navajo', 'nv', 'nav', 'ltr'],
        [112, 'Ndonga', 'ng', 'ndo', 'ltr'],
        [113, 'Nepali', 'ne', 'nep', 'ltr'],
        [114, 'North Ndebele', 'nd', 'nde', 'ltr'],
        [115, 'Northern Sami', 'se', 'sme', 'ltr'],
        [116, 'Norwegian Bokmål', 'nb', 'nob', 'ltr'],
        [117, 'Norwegian Nynorsk', 'nn', 'nno', 'ltr'],
        [118, 'Nuosu', 'ii', 'iii', 'ltr'],
        [119, 'Occitan', 'oc', 'oci', 'ltr'],
        [120, 'Ojibwe', 'oj', 'oji', 'ltr'],
        [121, 'Old Slavonic', 'cu', 'chu', 'ltr'],
        [122, 'Oriya', 'or', 'ori', 'ltr'],
        [123, 'Oromo', 'om', 'orm', 'ltr'],
        [124, 'Ossetian', 'os', 'oss', 'ltr'],
        [125, 'P?li', 'pi', 'pli', 'ltr'],
        [126, 'Panjabi', 'pa', 'pan', 'ltr'],
        [127, 'Pashto', 'ps', 'pus', 'rtl'],
        [128, 'Persian', 'fa', 'per', 'rtl'],
        [129, 'Polish', 'pl', 'pol', 'ltr'],
        [130, 'Portuguese', 'pt', 'por', 'ltr'],
        [131, 'Quechua', 'qu', 'que', 'ltr'],
        [132, 'Romanian', 'ro', 'rum', 'ltr'],
        [133, 'Romansh', 'rm', 'roh', 'ltr'],
        [134, 'Russian', 'ru', 'rus', 'ltr'],
        [135, 'Samoan', 'sm', 'smo', 'ltr'],
        [136, 'Sango', 'sg', 'sag', 'ltr'],
        [137, 'Sanskrit', 'sa', 'san', 'ltr'],
        [138, 'Sardinian', 'sc', 'srd', 'ltr'],
        [139, 'Serbian', 'sr', 'srp', 'ltr'],
        [140, 'Shona', 'sn', 'sna', 'ltr'],
        [141, 'Sindhi', 'sd', 'snd', 'ltr'],
        [142, 'Sinhala', 'si', 'sin', 'ltr'],
        [143, 'Slovak', 'sk', 'slo', 'ltr'],
        [144, 'Slovene', 'sl', 'slv', 'ltr'],
        [145, 'Somali', 'so', 'som', 'ltr'],
        [146, 'South Ndebele', 'nr', 'nbl', 'ltr'],
        [147, 'Southern Sotho', 'st', 'sot', 'ltr'],
        [148, 'Spanish', 'es', 'spa', 'ltr'],
        [149, 'Sundanese', 'su', 'sun', 'ltr'],
        [150, 'Swahili', 'sw', 'swa', 'ltr'],
        [151, 'Swati', 'ss', 'ssw', 'ltr'],
        [152, 'Swedish', 'sv', 'swe', 'ltr'],
        [153, 'Tagalog', 'tl', 'tgl', 'ltr'],
        [154, 'Tahitian', 'ty', 'tah', 'ltr'],
        [155, 'Tajik', 'tg', 'tgk', 'ltr'],
        [156, 'Tamil', 'ta', 'tam', 'ltr'],
        [157, 'Tatar', 'tt', 'tat', 'ltr'],
        [158, 'Telugu', 'te', 'tel', 'ltr'],
        [159, 'Thai', 'th', 'tha', 'ltr'],
        [160, 'Tibetan', 'bo', 'tib', 'ltr'],
        [161, 'Tigrinya', 'ti', 'tir', 'ltr'],
        [162, 'Tonga', 'to', 'ton', 'ltr'],
        [163, 'Tsonga', 'ts', 'tso', 'ltr'],
        [164, 'Tswana', 'tn', 'tsn', 'ltr'],
        [165, 'Turkish', 'tr', 'tur', 'ltr'],
        [166, 'Turkmen', 'tk', 'tuk', 'ltr'],
        [167, 'Twi', 'tw', 'twi', 'ltr'],
        [168, 'Uighur', 'ug', 'uig', 'ltr'],
        [169, 'Ukrainian', 'uk', 'ukr', 'ltr'],
        [170, 'Urdu', 'ur', 'urd', 'rtl'],
        [171, 'Uzbek', 'uz', 'uzb', 'ltr'],
        [172, 'Venda', 've', 'ven', 'ltr'],
        [173, 'Vietnamese', 'vi', 'vie', 'ltr'],
        [174, 'Volapük', 'vo', 'vol', 'ltr'],
        [175, 'Walloon', 'wa', 'wln', 'ltr'],
        [176, 'Welsh', 'cy', 'wel', 'ltr'],
        [177, 'Western Frisian', 'fy', 'fry', 'ltr'],
        [178, 'Wolof', 'wo', 'wol', 'ltr'],
        [179, 'Xhosa', 'xh', 'xho', 'ltr'],
        [180, 'Yiddish', 'yi', 'yid', 'rtl'],
        [181, 'Yoruba', 'yo', 'yor', 'ltr'],
        [182, 'Zhuang', 'za', 'zha', 'ltr'],
        [183, 'Zulu', 'zu', 'zul', 'ltr'],
    ];

    public function load(ObjectManager $manager): void
    {
        /** @var ClassMetadataInfo $metadata */
        $metadata = $manager->getClassMetadata(LanguageEntity::class);
        $metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new AssignedGenerator());

        foreach (self::$rows as $row) {
            $entity = new LanguageEntity($row[0]);
            $entity->setIso2($row[2]);
            $entity->setIso3($row[3]);
            $entity->setDirectionality($row[4]);
            $manager->persist($entity);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['binsoul/symfony-bundle-i18n'];
    }
}
