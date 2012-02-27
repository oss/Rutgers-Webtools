%define name webtools 
%define version 2.0
%define release 10.ru6
%define prefix /usr/local
%define _libdir %{prefix}/lib64

Summary: Core binaries, configs and templates for many Rutgers specific web applications (aka webtools). By default comes with the quota webtool to allow a user to check their quota via the web. 
Name: %name
Version: %version
Release: %release
License: GPL
Group: Services
Source0: http://jla.rutgers.edu/~brylon/Wget/webtools/linux/%{name}-%{version}.tgz
BuildRoot: %{_tmppath}/%{name}-root
BuildRequires: coreutils findutils quota 
Requires: quota findutils coreutils
Provides: webtools=%version
Conflicts: webtools<%version

%description
Core binaries, configs and templates for many Rutgers specific web applications 
(aka webtools). By default comes with the quota webtool to allow a user to check
 their quota via the web. 

%prep
%setup -q

%build
cd src/
make

%install

PATH="/usr/local/gnu/bin:$PATH"
export PATH

rm -rf $RPM_BUILD_ROOT
mkdir -p 0755 -p $RPM_BUILD_ROOT/usr/share/doc/%{name}-%{version}
mkdir -p 0755 $RPM_BUILD_ROOT%{_libdir}/webtools/config
mkdir -p 0755 $RPM_BUILD_ROOT%{_libdir}/webtools/templates
mkdir -p 0755 $RPM_BUILD_ROOT%{_libdir}/webtools/html/quota
mkdir -p 0511 $RPM_BUILD_ROOT%{_libdir}/webtools/webbin
mkdir -p 0710 $RPM_BUILD_ROOT%{_libdir}/webtools/bin

install -c -m 0644 $RPM_BUILD_DIR/%{name}-%{version}/README $RPM_BUILD_ROOT/usr/share/doc/%{name}-%{version}/
install -c -m 0644 $RPM_BUILD_DIR/%{name}-%{version}/Todo $RPM_BUILD_ROOT/usr/share/doc/%{name}-%{version}/
install -c -m 0644 $RPM_BUILD_DIR/%{name}-%{version}/Changelog $RPM_BUILD_ROOT/usr/share/doc/%{name}-%{version}/

install -c -m 4510 $RPM_BUILD_DIR/%{name}-%{version}/src/runas $RPM_BUILD_ROOT%{_libdir}/webtools/bin/
install -c -m 0511 $RPM_BUILD_DIR/%{name}-%{version}/src/makefile $RPM_BUILD_ROOT%{_libdir}/webtools/webbin/
install -c -m 0511 $RPM_BUILD_DIR/%{name}-%{version}/src/appendfile $RPM_BUILD_ROOT%{_libdir}/webtools/webbin/
install -c -m 0555 $RPM_BUILD_DIR/%{name}-%{version}/src/userinfo $RPM_BUILD_ROOT%{_libdir}/webtools/webbin/
install -c -m 0511 $RPM_BUILD_DIR/%{name}-%{version}/src/statfile $RPM_BUILD_ROOT%{_libdir}/webtools/webbin/
install -c -m 0511 $RPM_BUILD_DIR/%{name}-%{version}/src/readfile $RPM_BUILD_ROOT%{_libdir}/webtools/webbin/
install -c -m 0511 $RPM_BUILD_DIR/%{name}-%{version}/src/removefile $RPM_BUILD_ROOT%{_libdir}/webtools/webbin/
install -c -m 0555 $RPM_BUILD_DIR/%{name}-%{version}/src/copy $RPM_BUILD_ROOT%{_libdir}/webtools/webbin/
install -c -m 0511 $RPM_BUILD_DIR/%{name}-%{version}/src/linkfile $RPM_BUILD_ROOT%{_libdir}/webtools/webbin/
install -c -m 0555 $RPM_BUILD_DIR/%{name}-%{version}/src/move $RPM_BUILD_ROOT%{_libdir}/webtools/webbin/
install -c -m 0511 $RPM_BUILD_DIR/%{name}-%{version}/src/makedir $RPM_BUILD_ROOT%{_libdir}/webtools/webbin/
install -c -m 0511 $RPM_BUILD_DIR/%{name}-%{version}/src/listfile $RPM_BUILD_ROOT%{_libdir}/webtools/webbin/

install -c -m 0644 $RPM_BUILD_DIR/%{name}-%{version}/config/* $RPM_BUILD_ROOT%{_libdir}/webtools/config/
install -c -m 0644 $RPM_BUILD_DIR/%{name}-%{version}/templates/* $RPM_BUILD_ROOT%{_libdir}/webtools/templates/
find .
install -c -m 0644 $RPM_BUILD_DIR/%{name}-%{version}/html/functions.php $RPM_BUILD_ROOT%{_libdir}/webtools/html/
install -c -m 0644 $RPM_BUILD_DIR/%{name}-%{version}/html/quota/* $RPM_BUILD_ROOT%{_libdir}/webtools/html/quota/

%post
echo "README is located at /usr/share/doc/%{name}-%{version}/README";

cp -p /usr/bin/find %{_libdir}/webtools/webbin/find
cp -p /usr/bin/quota %{_libdir}/webtools/webbin/quota
cp -p /bin/touch %{_libdir}/webtools/webbin/touch
chmod 555 %{_libdir}/webtools/webbin/find
chmod 555 %{_libdir}/webtools/webbin/quota
chmod 555 %{_libdir}/webtools/webbin/touch

%triggerin -- findutils
install -pm 555 /usr/bin/find %{_libdir}/webtools/webbin/

%triggerin -- quota
install -pm 555 /usr/bin/quota %{_libdir}/webtools/webbin/

%triggerin -- coreutils
install -pm 555 /bin/touch %{_libdir}/webtools/webbin/

%files
%attr(710,root,apache) %dir %{_libdir}/webtools/bin/
%attr(-,root,apache) %{_libdir}/webtools/bin/runas
%{_libdir}/webtools/config/*
%{_libdir}/webtools/html/quota/*
%{_libdir}/webtools/html/functions.php
%{_libdir}/webtools/templates/*
%attr(511,root,apache) %dir %{_libdir}/webtools/webbin/
%{_libdir}/webtools/webbin/*
%{_docdir}/webtools-%{version}/*


%changelog
* Fri Feb 17 2012 Jarek Sedlacek <jarek@nbcs.rutgers.edu> - 2.0-10.ru6
- updated source with some files from solaris webtools package:
- copy,linkfile, listfile,move,readfile, and removefile
* Mon Jan 09 2012 Josh Matthews <jam761@nbcs.rutgers.edu> 2.0-8.ru6
- rebuilt for centos 6

* Tue Nov 29 2011 Phillip Quiza <pquiza@nbcs.rutgers.edu> 2.0-8.ru
- updated source

* Tue Nov 15 2011 Phillip Quiza <pquiza@nbcs.rutgers.edu> 2.0-7.ru
- updated source 

* Fri Oct 28 2011 Phillip Quiza <pquiza@nbcs.rutgers.edu> 2.0-6.ru
- Fixed bug: lowercase auth'd NetID (ie, REMOTE_USER) in config.php.sample 
- Changed %{_libdir} from /usr/lib64 to /usr/local/lib64

* Thu Aug 11 2011 Steven Lu <sjlu@nbcs.rutgers.edu> - 2.0-5.ru
- bug fixes to php code
- added in quota_config.php.sample

* Wed Aug 03 2011 Phillip Quiza <pquiza@nbcs.rutgers.edu> - 2.0-4.ru
- new features added to php code
- Added Todo and Changelog files
 
* Thu May 19 2011 Jarek Sedlacek <jarek@nbcs.rutgers.edu> - 2.0-3.ru
- added appendfile, makefile, adn statfile
- changed ln to cp in %post
* Tue May 10 2011 Jarek Sedlacek <jarek@nbcs.rutgers.edu> - 2.0-1.ru
- version bump 
- Removed check-criteria
- Removed restoremail 
* Tue Jun 01 2010 Orcan Ogetbil <orcan@nbcs.rutgers.edu> - 1.0-12.ru
- restoremail: Remove the previous RESTORE folder before restoring

* Thu May 20 2010 Orcan Ogetbil <orcan@nbcs.rutgers.edu> - 1.0-11.ru
- Add Requires: maildrop, as it is needed by restormail

* Wed May 19 2010 Orcan Ogetbil <orcan@nbcs.rutgers.edu> - 1.0-10.ru
- Fix restoremail for folders with whitespaces

* Tue Mar 16 2010 Naveen Gavini <ngavini@nbcs.rutgers.edu> 1.0-9.ru
- Updated check-criteria for roundcube

* Wed Feb 24 2010 Orcan Ogetbil <orcan@nbcs.rutgers.edu> 1.0-8.ru
- Copy the original find, copy and touch binaries to webbin/ dir with 555 perm
- Add triggers so that this is done on every update of corresponding packages

* Mon Feb 22 2010 Orcan Ogetbil <orcan@nbcs.rutgers.edu> 1.0-7.ru
- Actually do the ln's and chmod's in %%post

* Thu Feb 18 2010 Naveen Gavini <ngavini@nbcs.rutgers.edu> 1.0-6.ru
- Added check-criteria
- Added restoremail
* Mon Oct 19 2009 Naveen Gavini <ngavini@nbcs.rutgers.edu> 1.0-5.ru
- Cleaned up *.sample files.
- Add favicon, correct copyright date, and w3c valid image src link is
  now local in template file. 
* Thu Oct 15 2009 Jarek Sedlacek <jarek@nbcs.rutgers.edu> 1.0-4.ru
- set owner of %{_libdir}/webtools/bin/runas as root:apache
- new source
* Wed Oct 14 2009 Orcan Ogetbil <orcan@nbcs.rutgers.edu> 1.0-2.ru
- New source
- Update /usr/lib to %{_libdir}
- Update directory permissions
- Add "chmod 555 ..." messages to %post

* Tue Sep 29 2009 Naveen Gavini <ngavini@nbcs.rutgers.edu> 1.0-1.ru
- Initial Build.
