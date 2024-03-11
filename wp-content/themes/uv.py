import requests
import json
from datetime import datetime, timedelta
import plotly.graph_objects as go
import io
import base64
import sys
import pytz

# Get UV graph from lat and lng
def get_uv_graph(lat, lng):

# Request UV index data from the API
    try:
        response = requests.get(f"https://currentuvindex.com/api/v1/uvi?latitude={lat}&longitude={lng}")
        response.raise_for_status()
        data = json.loads(response.text)

# Extract and adjust times from the forecast data
        times = [item['time'] for item in data['forecast']]
        adjusted_times = [(datetime.fromisoformat(time.replace('Z', '+00:00')) + timedelta(hours=10)).isoformat() for time in times]
        uvi_values = [item["uvi"] for item in data['forecast']]

# Get current and next day's date in specific timezone
        now = datetime.now(pytz.timezone('Australia/Victoria'))
        day = datetime.now(pytz.timezone('Australia/Victoria')) + timedelta(days=1)

# Filter times and UV indices for the next day      
        day_times = [time for time in adjusted_times if datetime.fromisoformat(time).date() == day.date()]
        indexes = [index for index, time in enumerate(adjusted_times) if datetime.fromisoformat(time).date() == day.date()]
        uvi_for_day = [uvi_values[index] for index in indexes]
        max_uvi = max(uvi_for_day)
        current_uvi = data["now"]["uvi"]
        hours = [datetime.fromisoformat(time).hour for time in day_times]

# Create a graph
        fig = go.Figure()
        fig.add_trace(go.Scatter(x=hours, y=uvi_for_day, mode='lines+markers', name='UV Index'))
        now_str = now.strftime("%Y-%m-%dT%H:%M:%S%z")
        fig.add_trace(go.Scatter(x=[now.hour], y=[current_uvi], mode='markers', name='Current Time', marker=dict(color='red', size=10)))
        
        fig.update_layout(
            title=f"UV Index on {now.strftime('%Y-%m-%d %H:%M:%S %Z')} (Current: {current_uvi}, Max: {max_uvi})",
            xaxis_title="Hour of the Day",
            yaxis_title="UV Index",
            showlegend=True
        )
        fig.update_xaxes(range=[0, 23])
        fig.update_yaxes(range=[0, 16])  

        graph_json = fig.to_json()

        return graph_json, max_uvi
    except Exception as e:
        return f"Error: {e}"

if __name__ == "__main__":
    if len(sys.argv) == 3:
        latitude = float(sys.argv[1])
        longitude = float(sys.argv[2])
        result, max_uvi = get_uv_graph(latitude, longitude)
        print(result)
    else:
        print("Invalid arguments.")
