package com.gawdscape.statsync;

import org.bukkit.plugin.java.JavaPlugin;

import java.io.*;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.HashMap;
import java.util.UUID;

/**
 * Created by Vinnie on 12/30/2014.
 */
public class StatSync extends JavaPlugin {
    private final PlayerListener playerListener = new PlayerListener(this);
    private HashMap<String, Long> synced = new HashMap<>();

    public boolean shouldSync(String player) {
        long waitTime = getConfig().getInt("time") * 60000;
        if (synced.containsKey(player)) {
            if (System.currentTimeMillis() > (synced.get(player) + waitTime))
                return true;
            else
                return false;
        } else {
            return true;
        }
    }

    public String syncStats(UUID uuid, String username) {
        if (uuid == null || username == null) {
            return null;
        }
        getLogger().info("Syncing stats for " + username);
        File statFile = new File(getConfig().getString("world") + "/stats/" + uuid.toString() + ".json");
        StringBuilder args = new StringBuilder();
        args.append("uuid=" + uuid.toString());
        args.append("&name=" + username);
        args.append("&data=");
        BufferedReader br = null;
        try {
            String currentLine;
            br = new BufferedReader(new FileReader(statFile));
            while ((currentLine = br.readLine()) != null) {
                args.append(currentLine);
            }
        } catch (IOException e) {
            getLogger().severe(e.getMessage());
        } finally {
            try {
                if (br != null)
                    br.close();
            } catch (IOException ex) {
                getLogger().severe(ex.getMessage());
            }
        }
        String response = postHttp(args.toString());
        if (response != null) {
            synced.put(username, System.currentTimeMillis());
            return response;
        }
        return null;
    }

    public String postHttp(String args) {
        HttpURLConnection connection = null;
        try {
            URL url = new URL(getConfig().getString("url"));
            connection = (HttpURLConnection) url.openConnection();
            connection.setDoOutput(true);
            connection.setDoInput(true);
            connection.setInstanceFollowRedirects(false);
            connection.setRequestMethod("POST");
            connection.setRequestProperty("Content-Type", "application/x-www-form-urlencoded");
            connection.setRequestProperty("Content-Length", Integer.toString(args.getBytes().length));
            connection.setRequestProperty("X-Requested-With", getConfig().getString("requester"));
            connection.setUseCaches(false);

            DataOutputStream wr = new DataOutputStream(connection.getOutputStream());
            wr.writeBytes(args);
            wr.flush();
            wr.close();

            StringBuilder response = new StringBuilder();
            InputStream is = connection.getInputStream();
            BufferedReader rd = new BufferedReader(new InputStreamReader(is));
            String line;
            while ((line = rd.readLine()) != null) {
                response.append(line + "\n");
            }
            rd.close();
            return response.toString();
        } catch (Exception e) {
            getLogger().severe(e.getMessage());
            return null;
        } finally {
            if (connection != null) {
                connection.disconnect();
            }
        }
    }

    @Override
    public void onEnable() {
        getServer().getPluginManager().registerEvents(playerListener, this);
        getCommand("statsync").setExecutor(new SyncCommand(this));
    }

    @Override
    public void onDisable() {
        saveDefaultConfig();
    }
}
