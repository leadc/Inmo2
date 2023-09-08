import { Component, OnInit } from '@angular/core';
import { TranslateService } from '@ngx-translate/core';

@Component({
  selector: 'app-lang',
  templateUrl: './lang.component.html',
  styleUrls: ['./lang.component.scss']
})
export class LangComponent implements OnInit { 
  public langName = '';
  constructor(public translateService: TranslateService) { }

  ngOnInit() {  
   this.langName = this.getLangName(this.translateService.getDefaultLang());  
   console.log(this.translateService.translations);
  } 

  public changeLang(lang:string){ 
    this.translateService.use(lang); 
    this.langName = this.getLangName(lang);  
  } 

  public getLangName(lang:string){
    // console.log(this.translateService.translations);
    if(lang == 'es'){
      return 'Español';
    }
    else if(lang == 'en'){
      return 'English';
    }
    else if(lang == 'de'){
      return 'German';
    }
    
    else if(lang == 'fr'){
      return 'French';
    }
    else if(lang == 'ru'){
      return 'Russian';
    }
    else if(lang == 'tr'){
      return 'Turkish';
    }
    else{
      return 'Spanish';
    } 
  }

}
