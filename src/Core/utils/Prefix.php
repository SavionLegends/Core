<?php
/**
 * Created by PhpStorm.
 * User: Savion
 * Date: 4/28/2017
 * Time: 6:40 PM
 */

namespace Core\utils;

use pocketmine\utils\TextFormat as TF;


class Prefix{

    /*
     * CONTAINS ALL THE PREFIXES/MESSAGES I USE
     */

    const DEFAULT = TF::BOLD.TF::AQUA."Core".TF::GOLD.">".TF::RESET.TF::WHITE." ";
    const DEFAULT_BAD = TF::BOLD.TF::AQUA."Core".TF::GOLD.">".TF::RESET.TF::RED." ";
    const LOGIN = TF::BOLD.TF::AQUA."Core".TF::GOLD.">".TF::RESET.TF::WHITE." Please type your password to login!";
    const REGISTER = TF::BOLD.TF::AQUA."Core".TF::GOLD.">".TF::RESET.TF::WHITE." Please type your password in the chat to register!";
    const LOGGED_IN = TF::BOLD.TF::AQUA."Core".TF::GOLD.">".TF::RESET.TF::WHITE." You are now logged in! Have fun!";
    const PLAYER_NOT_ONLINE = TF::BOLD.TF::AQUA."Core".TF::GOLD.">".TF::RESET.TF::RED." That player is not online!";
    const PASSWORD_INCORRECT = TF::BOLD.TF::AQUA."Core".TF::GOLD.">".TF::RESET.TF::RED." Password is incorrect! Try again!";
    const PASSWORD_IN_CHAT = TF::BOLD.TF::AQUA."Core".TF::GOLD.">".TF::RESET.TF::RED." Please don't type your password in chat!";
}