<?php
/**
 * Created by PhpStorm.
 * User: 20deavaults
 * Date: 9/20/18
 * Time: 9:12 AM
 */

namespace Core\utils;



class Permissions{

   public static $RANKS = ["default", "owner", "builder", "admin", "moderator"];
   public static $RANKS_PERMISSIONS = ["owner" => [self::ALL_PERMS], "default" => [] ];
   public static $ALL_PERMISSIONS = ["core.permission.default", "core.permissions.build", "core.permissions.place", "core.permissions.commands.lang", "core.permissions.commands.lobby", "core.permissions.commands.npc", "core.permissions.commands.permission", "core.permissions.commands.quit", "core.permissions.commands.stats", "core.permissions.commands.iron_soup", "core.permissions.commands.kohi", "core.permissions.commands.help", "core.permissions.commands.me", "core.permissions.commands.tell", "core.permissions.*"];

    const DEFAULT = "core.permission.default";
    const BUILD = "core.permission.default";
    const PLACE = "core.permissions.place";
    const LANG_COMMAND = "core.permissions.commands.lang";
    const LOBBY_COMMAND = "core.permissions.commands.lobby";
    const NPC_COMMAND = "core.permissions.commands.npc";
    const PERMISSION_COMMAND = "core.permissions.commands.permission";
    const QUIT_COMMAND = "core.permissions.commands.quit";
    const STATS_COMMAND = "core.permissions.commands.stats";
    const IRON_SOUP_COMMAND = "core.permissions.commands.iron_soup";
    const KOHI_COMMAND = "core.permissions.commands.kohi";
    const HELP_COMMAND = "core.permissions.commands.help";
    const ME_COMMAND = "core.permissions.commands.me";
    const TELL_COMMAND = "core.permissions.commands.tell";
    const ALL_PERMS = "core.permissions.*";
}