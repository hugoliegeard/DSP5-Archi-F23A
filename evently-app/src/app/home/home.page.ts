import { Component } from '@angular/core';
import {HttpClient} from "@angular/common/http";

@Component({
  selector: 'app-home',
  templateUrl: 'home.page.html',
  styleUrls: ['home.page.scss'],
})
export class HomePage {

  events: any;

  constructor(private http: HttpClient) {
    this.http.get('https://localhost:8000/api/events').subscribe(events => {
      this.events = events;
    });
  }

}
