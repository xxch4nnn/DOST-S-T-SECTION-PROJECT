{{-- Folder Footer Background - Dual Layer Curved Folder Component --}}
<div style="line-height: 0;">
    <svg viewBox="0 0 1440 360" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg"
         style="display: block; width: 100%; height: 28vh; min-height: 160px;">
        <defs>
            <filter id="folder-shadow" x="-5%" y="-10%" width="110%" height="120%">
                <feDropShadow dx="0" dy="-3" stdDeviation="4" flood-color="#000000" flood-opacity="0.12"/>
            </filter>
        </defs>

        {{-- Dark Blue Folder Layer (High on left, straight horizontal -> S-curve down -> straight horizontal to right edge) --}}
        <path d="M 0,360 V 60 H 520 C 640,60 720,200 840,200 H 1440 V 360 Z" fill="#0066b2" filter="url(#folder-shadow)"/>

        {{-- Light Main Blue Folder Layer (Low on left, straight horizontal -> S-curve up -> straight horizontal to right edge) --}}
        <path d="M 0,360 V 270 H 520 C 640,270 720,160 840,160 H 1440 V 360 Z" fill="#54bbff" filter="url(#folder-shadow)"/>
    </svg>
</div>
