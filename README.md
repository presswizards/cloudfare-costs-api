# openai-costs-api
A PHP script to display the last X months of OpenAI costs per day with a total amount, up to 6 months.

## Useage

- Download and save the script to your favorite place to run PHP.
- Run the script using this format:

php path/scriptname OpenAI-Admin-Key [1-6]

As an examplem, for 3 months:

```
php ./openai-use.php sk-admin-123456-your-ADMIN-key-here 3
```

It will output:

```
...
Date: 03/08/25 $0.0708348 USD
Date: 03/11/25 $0.0016610 USD
Date: 03/13/25 $0.0104929 USD
Date: 03/19/25 $0.0001011 USD
Date: 03/20/25 $0.0018821 USD
Date: 03/24/25 $0.0219109 USD
Date: 03/27/25 $0.0548015 USD
Date: 03/28/25 $0.0018304 USD
Date: 03/29/25 $0.0020064 USD
Date: 03/31/25 $0.0025191 USD
Total for 2025-03: $0.2167544 USD

Date: 04/01/25 $0.5601888 USD
Total for 2025-04: $0.5601888 USD

Total Cost 03/04/25 to 04/03/25: $0.7769432 USD
```

## Get Your Admin Key Here

https://platform.openai.com/settings/organization/admin-keys


<p/>
<a href="https://www.buymeacoffee.com/robwpdev" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/default-orange.png" alt="Buy Me A Coffee" height="41" width="174"></a><br>
If this script saves you time, helps your clients, or helps you do better work, I’d appreciate it.
</p>
