import pandas as pd
import matplotlib.pyplot as plt
import mysql.connector

# Coneect to out database
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="root",
    database="Sun"
)

# SQL query for data
mortality_query = "SELECT Age_group_years, SUM(Count) AS Total_Count FROM mortality_table GROUP BY Age_group_years"

# Excute the query and store in DF
mortality_data = pd.read_sql_query(mortality_query, conn)

# SQL query for data
incidence_query = "SELECT Age_group_years, SUM(Count) AS Total_Count FROM incidence_table GROUP BY Age_group_years"

# Excute the query and store in DF
incidence_data = pd.read_sql_query(incidence_query, conn)


# Create grapgh
plt.figure(figsize=(12, 8))
plt.bar(incidence_data['Age_group_years'], incidence_data['Total_Count'], label='Incidence', alpha=0.7, color='blue')
plt.bar(mortality_data['Age_group_years'], mortality_data['Total_Count'], label='Mortality', alpha=0.7, color='orange')
plt.xlabel('Age Group')
plt.ylabel('Total Count')
plt.title('Cancer Mortality vs Incidence by Age Group')
legend_colors = {'Mortality': 'orange', 'Incidence': 'blue'}
plt.xticks(rotation=45)
handles = [plt.Rectangle((0,0),1,1, color=color, alpha=0.7) for color in legend_colors.values()]
plt.legend(handles, legend_colors.keys())
plt.tight_layout()
plt.savefig('/var/www/html/wordpress/wp-content/themes/cancer_graph7.png')
plt.close()
