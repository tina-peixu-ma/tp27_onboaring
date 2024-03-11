import sys
import json

# Calculate sunscreen portions depends one UV levels and clothes
def sunscreen_cal(hat, cloth_upper, cloth_lower, shoes, current_uv):
    
    sunscreen_volume = (-hat - 12 * cloth_upper - 5.8 * cloth_lower - 1.5 * shoes + 44.6) * (0.65 + 0.05 * current_uv)
    if 3 <= current_uv <= 12:
        recommendation_sunscreen_volume = round(sunscreen_volume, 1)
        message = (f"You need to apply approximately {recommendation_sunscreen_volume}ml of sunscreen every 2 hours when outside. "
                   "Apply sunscreen to any exposed skin at least 20 minutes before you go outside. "
                   "No sunscreen provides 100% protection so always use with a broad brimmed hat, sunglasses, covering clothing and shade.")
    elif current_uv > 12:
        recommendation_sunscreen_volume = round(sunscreen_volume * 1.25, 1)
        message = (f"You need to apply approximately {recommendation_sunscreen_volume}ml of sunscreen every 2 hours when outside. "
                   "Apply sunscreen to any exposed skin at least 20 minutes before you go outside. "
                   "No sunscreen provides 100% protection so always use with a broad brimmed hat, sunglasses, covering clothing and shade.")
    else:
        recommendation_sunscreen_volume = 0
        message = "Current UV index levels are low, you do not need sunscreen unless you have been under the sun for more than 2-3 hours."

    return {"volume": recommendation_sunscreen_volume, "message": message}
 
    
    
    return {"volume": recommendation_sunscreen_volume, "message": message}

# Convert command line arguments to variables
hat = int(sys.argv[1])
cloth_upper = int(sys.argv[2])
cloth_lower = int(sys.argv[3])
shoes = int(sys.argv[4])
current_uv = float(sys.argv[5])

# Call the sunscreen calculator function and print the result
result = sunscreen_cal(hat, cloth_upper, cloth_lower, shoes, current_uv)
print(json.dumps(result))
