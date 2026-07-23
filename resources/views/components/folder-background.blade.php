{{-- Folder Footer Background - Dual Layer Curved Folder Component --}}
<div style="line-height: 0;">
    <svg viewBox="0 0 1440 360" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg"
         style="display: block; width: 100%; height: 28vh; min-height: 160px;">
        <defs>
            <filter id="folder-shadow" x="-5%" y="-10%" width="110%" height="120%">
                <feDropShadow dx="0" dy="-3" stdDeviation="4" flood-color="#000000" flood-opacity="0.12"/>
            </filter>
        </defs>

        {{-- Dark Blue Folder Layer (Rounded outer edges, steeper central transition >45deg) --}}
        <path d="M 0,360 V 76 Q 0,50 26,50 H 610 C 670,50 710,210 770,210 H 1414 Q 1440,210 1440,236 V 360 Z" fill="#0066b2" filter="url(#folder-shadow)"/>

        {{-- Light Main Blue Folder Layer (Rounded outer edges, steeper central transition >45deg) --}}
        <path d="M 0,360 V 286 Q 0,260 26,260 H 610 C 670,260 710,140 770,140 H 1414 Q 1440,140 1440,166 V 360 Z" fill="#54bbff" filter="url(#folder-shadow)"/>
    </svg>
</div>
