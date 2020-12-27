import { Component, OnInit } from '@angular/core';
import { FormGroup, FormControl, FormBuilder, Validators, NgForm } from '@angular/forms';
import { first } from 'rxjs/operators';
import { Router } from '@angular/router';
import { AuthService } from '../auth.service';
@Component({
  selector: 'app-signup',
  templateUrl: './signup.component.html',
  styleUrls: ['./signup.component.css']
})
export class SignupComponent implements OnInit {

  loginForm: FormGroup;
      constructor(private fb: FormBuilder, private authService: AuthService ,private router:Router) {
      this.loginForm = this.fb.group({
      username: ['', [Validators.required,Validators.minLength(1), Validators.email]],
      password: ['', Validators.required],
      name: ['', Validators.required],
      });
      }

  ngOnInit(): void {
  }
  get name() { return this.loginForm.get('name'); }
  get username() { return this.loginForm.get('username'); }
  get password() { return this.loginForm.get('password'); }


  onSubmit()
{
  // console.log();
this.authService.signup(this.name.value,this.username.value,this.password.value)
.pipe(first())
.subscribe(
data => {
this.router.navigate(['login']);
},

error => {
});
}

}
