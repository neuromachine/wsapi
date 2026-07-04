import json
import os
import urllib.request
import urllib.parse
import time

files = [
    'promo-i-korporativnoe-video.json',
    'publikacii-i-stati-v-smi.json',
    'rabota-s-otzyvami-reputaciei.json',
    'retargeting-i-dinamiceskie-kampanii.json',
    'saas-servis-veb-prilozenie.json',
    'seo-teksty-opisaniia-tovarov.json',
    'smm-instagram-vk-telegram-i-dr.json',
    'soprovozdenie-startapov.json',
    'sozdanie-utp-i-pozicionirovanie.json',
    'targetirovannaia-reklama.json',
    'ulucsenie-konversii.json',
    'ux-i-texnicnyi-kopiraiting.json',
    'video-dlia-socsetei-reels.json',
    'vnutrenniaia-i-vnesniaia-optimizaciia.json',
    'vorksopy-po-prodvizeniiu.json',
    'zashhita-i-bezopasnost.json'
]

dir_path = r'c:\OSPanel\home\wsapi\storage\app\blocks\items'

def translate(text, target_lang='en', source_lang='ru'):
    if not text: return text
    url = f"https://translate.googleapis.com/translate_a/single?client=gtx&sl={source_lang}&tl={target_lang}&dt=t&q={urllib.parse.quote(text)}"
    try:
        req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
        with urllib.request.urlopen(req) as response:
            result = json.loads(response.read().decode('utf-8'))
            translated = ''.join([sentence[0] for sentence in result[0]])
            return translated
    except Exception as e:
        print(f"Error translating: {text[:20]}... Error: {e}")
        time.sleep(1) # wait and retry once
        try:
            req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
            with urllib.request.urlopen(req) as response:
                result = json.loads(response.read().decode('utf-8'))
                translated = ''.join([sentence[0] for sentence in result[0]])
                return translated
        except:
            return text

for file_name in files:
    file_path = os.path.join(dir_path, file_name)
    print(f"Processing {file_name}...")
    with open(file_path, 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    if 'items' in data:
        items = data['items']
        
        # determine main item (if length > 1, usually the 2nd one, otherwise the 1st)
        main_index = 1 if len(items) > 1 else 0
        
        for i, item in enumerate(items):
            # Format and clear
            item['name'] = item['name'].strip()
            if 'properties' in item:
                if 'descr' in item['properties']:
                    item['properties']['descr'] = item['properties']['descr'].strip()
                if 'features' in item['properties']:
                    # Remove duplicates and strip
                    seen = set()
                    new_features = []
                    for feature in item['properties']['features']:
                        f_stripped = feature.strip()
                        if f_stripped and f_stripped not in seen:
                            seen.add(f_stripped)
                            new_features.append(f_stripped)
                    item['properties']['features'] = new_features
            
            # Featured flag
            if i == main_index:
                item['featured'] = True
            else:
                if 'featured' in item:
                    del item['featured']
            
            # Translation
            en_props = {}
            if 'properties' in item:
                if 'descr' in item['properties']:
                    en_props['descr'] = translate(item['properties']['descr'])
                if 'features' in item['properties']:
                    en_props['features'] = [translate(f) for f in item['properties']['features']]
            
            en_name = translate(item['name'])
            
            item['en'] = {
                'name': en_name,
                'properties': en_props
            }
            
            # Optional: ensure 'vi' is preserved if we want, but the prompt only asked for 'en' translation
            # We'll just add 'en'.
            
    with open(file_path, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=4)

print("Done.")
