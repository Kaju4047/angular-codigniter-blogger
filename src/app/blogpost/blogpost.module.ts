import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { BlogpostRoutingModule } from './blogpost-routing.module';
import { BlogpostListComponent } from './blogpost-list/blogpost-list.component';
import { BlogpostDetailComponent } from './blogpost-detail/blogpost-detail.component';

@NgModule({
  imports: [
    CommonModule,
    BlogpostRoutingModule
  ],
  exports: [
  ],
  declarations: [ BlogpostListComponent, BlogpostDetailComponent]
})
export class BlogpostModule { }
