<?php
/*
 * help.php
 */
//require('user_authentication.php');
include('common_functions.php');
//$refindex = 1;
//echo $refindex; $refindex++;
echo(html_header());
?>      
        <div class="datacontainer">
            <h2>Beregningsmetoder og referencer</h2>
                <h4>Korrektion fra EDTA til inulin</h4>
                <div class="body_text">
                    Alle beregninger er korrigeret for 10% lavere renal clearance af EDTA i forhold til inulin. Der tages samtidig højde for en konstant udskillelse svarende til 3.7 ml/min andre steder
                    end nyrerne som foreskrevet af [1]. Korrektionen fra EDTA til inulin bliver derfor:<br/>
                    <div class="formula">
                        GFR(ml/min) = (<sup>51</sup>CR-EDTA - 3.7(ml/min)) x 1.1
                    </div>
                </div>
                <h4>Overfladearealbestemmelse</h4>
                <div class="body_text">
                    Overfladearealet af en patient kan enten beregnes fra Haycock (standard) [2] eller Du Bois (kan vælges) [3]. For Haycock beregnes arealet som
                    <div class="formula">
                        BSA(m<sup>2</sup>) = 0.024265 x W<sup>0.5378</sup>(kg) x H<sup>0.3964</sup>(cm)
                    </div>
                    hvor W er patientens vægt (kg) og H er patientens højde (cm). Du Bois beregnes som
                    <div class="formula">
                        BSA(m<sup>2</sup>) = 0.007184 x W<sup>0.425</sup>(kg) x H<sup>0.725</sup>(cm)
                    </div>
                </div>
                <h4>Beregning af alder</h4>
                <div class="body_text">
                    Alder på patienter er beregnet i forhold til undersøgelsestidspunktet og <b>ikke</b> dags dato
                </div>
                <h4>Estimering af ECV</h4>
                <div class="body_text">
                    Estimering af ECV foretages via Bird's-estimat [13] og er bestemt som
                    <div class="formula">
						ECV<sub>e</sub>(l) = 0.02154 x W<sup>0.06469</sup>(kg) x H<sup>0.7236</sup>(cm)
					</div>
                </div>
            <h4>Estimering af dobbeltbestemte prøver</h4>
				<div class="body_text">
					For dobbeltbestemte prøver vurderes ensarteheden som forskellen på antal counts i forhold til standardafvigelse på Poisson-fordelingen.<br/>
					&sigma; =
					<div class="fraction">
						<span class="fup">
							&Delta;
						</span>
						<span class="bar">/</span>
						<span class="fdn">
							&tau;
						</span>
					</div>
					<div class="fraction">
						<span class="fup">
							<sup>V<sub>2</sub></sup>&frasl;<sub>V<sub>1</sub></sub>(T1 - B1) - (T2 - B2)
						</span>
						<span class="bar">/</span>
						<span class="fdn">
							<sup>V<sub>2</sub></sup>&frasl;<sub>V<sub>1</sub></sub>&radic;<span style="text-decoration: overline">T1</span> + &radic;<span style="text-decoration: overline">T2</span> + &radic;<span style="text-decoration: overline">B1</span> + &radic;<span style="text-decoration: overline">B2</span>
						</span>
					</div>
					<br/>
					hvor TX er totalt antal counts for prøve med tilhørende baggrund BX. V<sub>x</sub> er volumen i henholdsvis prøve X. &sigma; farvekodes i svararket efter følgende system.
					<table class="reference">
						<tr>
							<th></th><th> &sigma; &le; 2</th><th> 2 < &sigma; < 2.5</th><th> &sigma; &ge; 2.5</th>
						</tr>
						<tr>
							<td>&sigma;</td><td><span style="color: green;">GRØN</span></td><td><span style="color: #ffcc00;">ORANGE</span></td><td><span style="color: RED;">RØD</span></td>
						</tr>
					</table>
				</div>
            <h3>1-punktsmålinger</h3>
                <h4>Voksne</h4>
                    <div class="body_text">
                        Voksnes standard-GFR beregnes fra Groth and Aasted-formlen [4] og beregnes som
                            <div class="formula">
                                stdGFR(ml/min/1.73m<sup>2</sup>) = (0.213 x T(min) - 104) x ln(Y<sub>t</sub>(cpm/ml) x A / Q<sub>0</sub>(cpm)) + 1.88 x T(min) - 928
                            </div>
                        hvor T  er tiden siden injektion (min), Y<sub>t</sub> er den målte aktivitet (cpm/ml), A er overfladearealet af patienten (m<sup>2</sup>) og Q<sub>0</sub> er total injiceret dosis (cpm)<br/>
                    </div>
                <h4>Børn</h4>
                    <div class="body_text">
                        Børn beregnes fra EANM-retningslinjer [5] efter følgende formel
                        <div class="formula">
                            stdGFR(ml/min/1.73m<sup>2</sup>) = ((2.602 x V<sub>120</sub>(l)) - 0.273) / BSA(m<sup>2</sup>)
                        </div>
                        hvor V<sub>120</sub> er det virtuelle distribuerede volumen (l) til tiden 120 min. postinjektion og BSA er overfladearealet af patienten (m<sup>2</sup>). V<sub>120</sub> beregnes som
                        <div class="formula">
                            V<sub>120</sub>(l) = Q<sub>0</sub>(cpm) / (P<sub>120</sub>(cpm/ml) x 1000)
                        </div>
                        hvor Q<sub>0</sub> er total injiceret dosis (cpm) og P<sub>120</sub> er en korrektionsfaktor, der anvendes hvis blodprøven ikke tages præcist efter 120 min. P<sub>120</sub> beregnes som
                        <div class="formula">
                            P<sub>120</sub>(cpm/ml) = Y<sub>t</sub>(cpm/ml) x exp(0.008 x (t(min) - 120))
                        </div>
                        Y<sub>t</sub> er den målte aktivitet (cpm/ml) og t er tiden siden injektion (min). <b>Ovenstående korrektion er kun gyldig i tidsintervallet 110-130 min postinjektion!</b>
                    </div>
            <h3>Flerpunktsmålinger</h3>
                <div class="body_text">
                    GFR fra EDTA-clearance-kurven beregnes ud fra analytisk formel foreslået af Brøchner-Mortensen og Jødal [6], der gælder for både børn og voksne.
                    <div class="formula">
                        stdGFR(ml/min/1.73m<sup>2</sup>) = Cl(counts/ml) / (1 + 0.00185 x BSA<sup>-0.3</sup>(m<sup>2</sup>) x Cl(counts/ml))
                    </div>
                    hvor Cl (counts/ml) beregnes som
                    <div class="formula">
                        Cl (counts/ml) = Q<sub>0</sub>(cpm) x b (min<sup>-1</sup>) / c (cpm/ml)
                    </div>
                    hvor c og b er fittingparametre fra det monoeksponetielle fit til clearance-kurven
                    <div class="formula">
                        c(cpm/ml) x exp(-b(min<sup>-1</sup>) x t(min))
                    </div>
                </div>
                <h5>Beregning af ekstracellulærvæske (ECV)</h5>
                <div class="body_text">
					Beregningen foretages på flerpunktsmålinger, hvor clearance-kurven er kendt. ECV beregnes som beskrevet i [9] som:
					<div class="formula">
						ECV = V<sub>1</sub>/(1 + 2 x f x Cl<sub>1</sub>)
					</div>
					hvor f = 0.0032 x BSA<sup>-1.3</sup>. V<sub>1</sub> beregnes som
					<div class="formula">
						V<sub>1</sub> = Q<sub>0</sub>/c
					</div>
					hvor c fås fra skæringen med y-aksen fra det monoekspontielle fit. Tilsvarende beregnes Cl<sub>1</sub> som:
					<div class="formula">
						Cl<sub>1</sub> = Q<sub>0</sub> x b / c
					</div>
					hvor b tilsvarende også bestemmes fra det monoeksponentielle fit.<br/><br/>
					Ydermere beregnes forholdet GFR/ECV der fortæller noget om hvor ofte "det som skal reguleres" (i.e. de ekstracellulære væsker) kommer i kontakt med "reguleringen" (i.e. nyrerne).
					45%/time betyder fx at volumet af plasma filtreret på 1 time udgør 45% af den samlede ekstracellulærevæskemængde. Desuden er det praktisk at kende forholdet 1 ml/min/l = 6%/time.
					
					
            <h3>Estimering af GFR (eGFR)</h3>
            
            <h4>Aldersbaseret eGFR</h4>
            Det forventede GFR for voksne (> 20 år) beregnes efter Brøchner-Mortensen [7], mens børn (< 2 år) beregnes efter Brøchner-Mortensen [8]. Læg mærke til, at der forventede GFR udelukkede er baseret på alder af patienten.
                <h5>Voksne (> 20 år)</h5>
                <div class="body_text">
                    Mænd i alderen 20-39 tilskrives et standard-GFR
                    på 111 ml/min. Kvinder i samme aldersgruppe tilskrives
                    <div class="formula">
                        std.-GFR(ml/min/1.73m<sup>2</sup>) = 111(ml/min) x 0.929 = 103 ml/min
                    </div>
                    For mænd over 40 år beregnes standard-GFR som
                    <div class="formula">
                        std.-GFR(ml/min/1.73m<sup>2</sup>) = -1.16 x alder(år) + 157.8
                    </div>
                    mens det for kvinder i samme aldersgruppe beregnes som
                    <div class="formula">
                        std.-GFR(ml/min/1.73m<sup>2</sup>) = -1.07 x alder(år) + 146
                    </div>
                </div>
                <h5>Unge (2 år < alder < 20 år)</h5>
                <div class="body_text">
                    For aldre mellem 2 år og 20 år tilskreves en konstant renal clearance for mænd og kvinder på 109 ml/min.
                </div>
                <h5>Børn (<2 år)</h5>
                <div class="body_text">
                    For børn (<2 år) beregnes std.-GFR som
                    <div class="formula">
                        stdGFR(ml/min/1.73m<sup>2</sup>) = 10<sup>0.209 x log<sub>10</sub>(alder(dage)) + 1.44)</sup>
                    </div>
                    hvor der ikke skelnes mellem køn. 
                </div>
                
                <h4>Beregning af eGFR via MDRD</h4>
                Den estimerede GFR beregnes på baggrund af P-kreatininniveau, køn og alder (beregnes kun for alder > 18 år). Til estimeringen anvendes MDRD-formlen [10]. Det estimerede GFR er korrigeret for standardoverfladeareal.
                <div class="formula">
                        eGFR(ml/min/1.73m<sup>2</sup>) = 175 x (S<sub>crea</sub>(&#181;mol/l) / 88.4)<sup>-1.154</sup> x Alder(år)<sup>-0.203</sup> 
                </div>
                Mens den for kvinder beregnes som
                 <div class="formula">
                        eGFR(ml/min/1.73m<sup>2</sup>) = 175 x (S<sub>crea</sub>(&#181;mol/l) / 88.4)<sup>-1.154</sup> x Alder(år)<sup>-0.203</sup> x 0.742
                </div>
                
                <h4>Beregning af eGFR via CKD-EPI</h4>
                Den estimerede GFR beregnes på baggrund af P-kreatininniveau, køn og alder. Til estimeringen anvendes CKD-EPI-formlen. Sammenligning mellem CKI-EPI eller MDRD kan findes i [11]. Det estimerede GFR er korrigeret for standardoverfladeareal.
                For kaukasiere &ge; 17 år estimeres GFR som:
                <table class="reference">
					<tr>
						<th>Køn</th><th>Serumkreatinin (&#181;mol/l)</th><th>Formel</th>
					</tr>
					<tr>
						<td rowspan="2">Kvinde</td>
						<td>&#8804;62</td>
						<td>144 &#215; (S<sub>crea</sub>(&#181;mol/l)/(0.7 &#215; 88.4)<sup>-0.329</sup> &#215; (0.993)<sup>alder(år)</sup></td>
					</tr>
					<tr>
						<td>>62</td>
						<td>144 &#215; (S<sub>crea</sub>(&#181;mol/l)/(0.7  &#215; 88.4))<sup>-1.209</sup> &#215; (0.993)<sup>alder(år)</sup></td>
					</tr>
					<tr>
						<td rowspan="2">Mand</td>
						<td>&#8804;80</td>
						<td>141 &#215; (S<sub>crea</sub>(&#181;mol/l)/((0.9 &#215; 88.4))<sup>-0.411</sup> &#215; (0.993)<sup>alder(år)</sup></td>
					</tr>
					<tr>
						<td>>80</td>
						<td>141 &#215; (S<sub>crea</sub>(&#181;mol/l)/(0.9 &#215; 88.4))<sup>-1.209</sup> &#215; (0.993)<sup>alder(år)</sup></td>
					</tr>
                </table>
                
                <h4>Beregning af eGFR via CKiD<sub>bedside</sub></h4>
                Den estimerede GFR beregnes på baggrund af P-kreatininniveau, alder og højde (beregnes kun for alder < 17 år). Til estimeringen anvendes CKiD<sub>bedside</sub> [12]. Det estimerede GFR er korrigeret for standardoverfladeareal.
                <div class="formula">
                        eGFR(ml/min/1.73m<sup>2</sup>) = 36.5 &times; højde(cm) /  S<sub>crea</sub>(&#181;mol/l)
                </div>
                
            <h3>Klassificering af nyrefunktion</h3>
            Nyrefunktionen klassificeres efter nuværende vejledning fra Dansk Selskab for Klinisk Biokemi og Dansk Nefrologisk Selskab [1]. Klassificeringen er aldersuafhængig og baseres på
            standard-GFR.
            <div class="body_text">
                <dl>
                    <dt>std.-GFR > 90</dt>
                        <dd>Normal eller forhøjet nyrefunktion</dd>
                    <dt>std.-GFR > 60 og stdGFR &#8804; 90</dt>
                        <dd>Let nedsat nyrefunktion</dd>
                    <dt>std.-GFR > 30 og stdGFR &#8804; 60</dt>
                        <dd>Moderat nedsat nyrefunktion</dd>
                    <dt>std.-GFR &#8805; 15 og stdGFR &#8804; 30</dt>
                        <dd>Svært nedsat nyrefunktion</dd>
                    <dt>std.-GFR < 15</dt>
                        <dd>Terminal nyreinsufficiens</dd>
                </dl>
            </div>
            
                         
            <h3>Referencer</h3>
            <div class="body_text">
                [1] <a href="pdf/klinisk_biokemi_dansk_nefrologisk_selskab_nyrefunktion_og_proteinuri.pdf">Metoder til vurdering af nyrefunktion og proteinuri, juni 2009, Dansk Selskab for Klinisk Biokemi og Dansk Nefrologisk Selskab</a><br/>
                [2] <a href="pdf/Haycock1978.pdf">Haycock et al., J. Pediat., 93:62-66, 1978</a><br/>
                [3] Du Bois et al., Arch. Intern. Med. 17:863-871, 1916<br/>
                [4] <a href="pdf/Groth1981.pdf">Groth and Aasted, Nucl. Med. Commun, 1:83-86, 1981</a><br/>
                [5] <a href="pdf/Piepsz2000.pdf">Piepsz et al., EANM guidelines for glomerular filtration rate determination in children, 2000</a> <br/>
                [6] <a href="pdf/Brøchner-Mortensen2009.pdf">Brøchner-Mortensen and Jødal, Scand. J. Clin & Lab. Invest., 69(3):314-322, 2009</a><br/>
                [7] <a href="pdf/Brøchner-Mortensen1977.pdf">Brøchner-Mortensen, Scand. J. Urol. Nephrol. 11:257-262, 1977</a> <br/>
                [8] <a href="pdf/Brøchner-Mortensen1982.pdf">Brøchner-Mortensen, Scand. J. Urol. Nephrol. 16:229-236, 1982</a><br/>
                [9] <a href="pdf/Jodal2012.pdf">Jødal and Brøchner-Mortensen, Nucl. Med. Comm., 33:1243-1253, 2012</a><br />
                [10] <a href="pdf/Levey1999.pdf">Levey et al., Ann. Intern. Med., 130(6):461, 1999</a><br />
                [11] <a href="pdf/Levey2009.pdf">Levey et al., Ann. Intern. Med., 150(9):604-12, 2009</a><br/>
                [12] <a href="pdf/Schwartz2009.pdf">Schwartz et al., J. Am. Soc. Nephrol., 20:629-637, 2009</a>
                [13] <a href="pdf/Bird2003.pdf">Bird et al., J. Nucl. Med., 44(7):1037-1043, 2003</a>
            </div>
        </div>
<?php
    echo(html_footer());
?>
