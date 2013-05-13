<?php

namespace Dat0r\Core\Runtime\Sham\Guesser;

use Dat0r\Core\Runtime\Module\IModule;
use Dat0r\Core\Runtime\Document\IDocument;
use Dat0r\Core\Runtime\Field;

/**
 * Guesser\TextField returns a Faker provider using only a given name.
 *
 * @author Steffen Gransow <graste@mivesto.de>
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 */
class TextField
{
    /**
     * @param string $name name to use for guessing a provider.
     * @param \Faker\Generator $generator instance with fake data providers to use for fake data generation
     *
     * @return Callable closure or NULL if guessing failed.
     */
    public static function guess($name, \Faker\Generator $generator)
    {
        $name = mb_strtolower($name);

        switch ($name)
        {
            case 'first_name':
            case 'firstname':
            case 'given_name':
            case 'givenname':
            {
                return function() use ($generator) { return $generator->firstName; };
                break;
            }
            case 'last_name':
            case 'lastname':
            case 'surname':
            case 'family_name':
            case 'familyname':
            {
                return function() use ($generator) { return $generator->lastName; };
                break;
            }
            case 'name':
            case 'author':
            case 'creator':
            case 'composer':
            case 'editor':
            case 'user':
            case 'writer':
            case 'novelist':
            case 'financier':
            case 'participant':
            case 'inviter':
            case 'invitee':
            case 'attender':
            case 'attendee':
            case 'attendant':
            case 'partner':
            case 'accomplice':
            case 'witness':
            case 'assistant':
            case 'aide':
            case 'helper':
            case 'associate':
            case 'colleague':
            case 'cohort':
            case 'fellow':
            case 'worker':
            case 'coworker':
            case 'employer':
            case 'employee':
            case 'manager':
            case 'boss':
            case 'principal':
            case 'head':
            case 'leader':
            case 'contributor':
            case 'donor':
            case 'spender':
            case 'sponsor':
            case 'benefactor':
            case 'presenter':
            case 'anchorman':
            case 'anchorwoman':
            case 'anchor':
            case 'moderator':
            case 'host':
            case 'co_host':
            case 'cohost':
            case 'tv_host':
            case 'tvhost':
            case 'quizmaster':
            {
                return function() use ($generator) { return $generator->name; };
                break;
            }
            case 'alias':
            case 'moniker':
            case 'handle':
            case 'username':
            case 'user_name':
            case 'login':
            case 'login_name':
            case 'nick':
            case 'nickname':
            case 'nick_name':
            {
                return function() use ($generator) { return $generator->userName; };
                break;
            }
            case 'email':
            case 'e_mail':
            case 'e-mail':
            {
                return function() use ($generator) { return $generator->email; };
                break;
            }
            case 'phone':
            case 'telephone':
            case 'phone_number':
            case 'phonenumber':
            case 'mobile':
            case 'mobile_phone':
            case 'cellphone':
            case 'cell_phone':
            case 'cellular':
            case 'cellular_phone':
            {
                return function() use ($generator) { return $generator->phoneNumber; };
                break;
            }
            case 'address':
            case 'adress':
            {
                return function() use ($generator) { return $generator->address; };
                break;
            }
            case 'city':
            {
                return function() use ($generator) { return $generator->city; };
                break;
            }
            case 'streetaddress':
            case 'street_address':
            case 'street':
            case 'street_number':
            case 'road':
            {
                return function() use ($generator) { return $generator->streetAddress; };
                break;
            }
            case 'postcode':
            case 'post_code':
            case 'postal_code':
            case 'postal_areacode':
            case 'postal_area_code':
            case 'postal_address':
            case 'zip_code':
            case 'zipcode':
            case 'zip':
            {
                return function() use ($generator) { return $generator->postcode; };
                break;
            }
            case 'country':
            case 'nation':
            {
                return function() use ($generator) { return $generator->country; };
                break;
            }
            case 'state':
            case 'federal_state':
            case 'federalstate':
            case 'federate_state':
            case 'federatestate':
            case 'province':
            {
                return function() use ($generator) { return $generator->state; };
                break;
            }
            case 'title':
            case 'headline':
            {
                return function() use ($generator) { return $generator->sentence; };
                break;
            }
            default:
            {
                return NULL;
                break;
            }
        }

        return NULL;
    }
}
