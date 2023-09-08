import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes } from '@angular/router';
import { SharedModule } from '../../shared/shared.module';
import { GoogleMapsModule } from '@angular/google-maps';  
import { InputFileModule } from 'src/app/theme/components/input-file/input-file.module';
import { SubmitPropertyComponent } from './submit-property.component';

export const routes: Routes = [
  { path: '', component: SubmitPropertyComponent, pathMatch: 'full'  }
];

@NgModule({
  declarations: [SubmitPropertyComponent],
  imports: [
    CommonModule,
    RouterModule.forChild(routes),
    SharedModule,
    GoogleMapsModule, 
    InputFileModule
  ]
})
export class SubmitPropertyModule { }
